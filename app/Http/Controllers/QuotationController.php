<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Models\Quotation;
use App\Models\QuoteSession;
use App\Models\Company;
use App\Models\Rate;
use App\Models\User;
use App\Notifications\QuotationStatusChanged;
use App\Services\PremiumCalculationService;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    protected $premiumCalculator;

    public function __construct(PremiumCalculationService $premiumCalculator)
    {
        $this->premiumCalculator = $premiumCalculator;
    }

    public function tracker()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        $query = Quotation::with(['user', 'company'])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id));

        $quotations = (clone $query)->latest()->paginate(20);

        $stats = [
            'total'     => (clone $query)->count(),
            'generated' => (clone $query)->where('status', 'generated')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];

        // Quotations grouped by underwriter / company.
        $byCompany = (clone $query)
            ->with('company')
            ->get()
            ->groupBy('company_id')
            ->map(function ($group) {
                $company = $group->first()->company;
                return [
                    'company'        => $company->name ?? 'Unknown',
                    'count'          => $group->count(),
                    'generated'      => $group->where('status', 'generated')->count(),
                    'completed'      => $group->where('status', 'completed')->count(),
                    'total_premium'  => $group->sum('total_premium'),
                ];
            })
            ->sortByDesc('count')
            ->values();

        // Quotations that have been issued an insurance certificate / sticker.
        $certified = (clone $query)
            ->where('status', 'completed')
            ->with(['user', 'company', 'stickers'])
            ->latest()
            ->take(20)
            ->get();

        return view('quotations.tracker', compact('quotations', 'stats', 'byCompany', 'certified'));
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate(['status' => 'required|string|in:generated,completed']);

        $oldStatus = $quotation->status;
        if ($oldStatus === $request->status) {
            return back()->with('info', "Quotation status is already {$request->status}.");
        }

        $quotation->update(['status' => $request->status]);

        AuditService::activity('quotation_status_changed', "Quotation {$quotation->quotation_number} changed from {$oldStatus} to {$request->status}");

        $admins = User::role(['super-admin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new QuotationStatusChanged($quotation, $oldStatus, $request->status));
        }
        try {
            $quotation->user?->notify(new QuotationStatusChanged($quotation, $oldStatus, $request->status));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to notify quotation owner: " . $e->getMessage());
        }

        return back()->with('success', "Quotation status updated to {$request->status}.");
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');
        
            // ── Base query (scope to own user unless admin) ──────────
            $query = Quotation::with(['user', 'company'])
                ->when(! $isAdmin, fn($q) => $q->where('user_id', $user->id))
                ->latest();
        
            // ── Paginated table results ──────────────────────────────
            $quotations = $query->paginate(20)->withQueryString();
        
            // ── Analytics (same scope as query above) ───────────────
            $analyticsBase = Quotation::query()
                ->when(! $isAdmin, fn($q) => $q->where('user_id', $user->id));

            $scope = $isAdmin ? null : $user->id;

            // ── Analytics (scoped to the user unless admin) ──────────
            $analytics = [
                'total'      => (clone $analyticsBase)->count(),
                'today'      => (clone $analyticsBase)->whereDate('created_at', today())->count(),
                'this_week'  => (clone $analyticsBase)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => (clone $analyticsBase)->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)->count(),

                // Daily trend — last 14 days (zero-filled)
                'trend' => $this->buildTrend($analyticsBase),

                // Per-company quotation count + total premium — for both charts
                'by_company' => Company::withCount(['quotations' => fn($q) => $scope ? $q->where('user_id', $scope) : $q])
                    ->withSum(['quotations' => fn($q) => $scope ? $q->where('user_id', $scope) : $q], 'total_premium')
                    ->get()
                    ->filter(fn($c) => $c->quotations_count > 0)
                    ->map(fn($c) => [
                        'company'       => $c->name,
                        'count'         => $c->quotations_count,
                        'total_premium' => (int) $c->quotations_sum_total_premium,
                    ])
                    ->values(),
            ];
                
            // ── Users list for admin "Apply for User" modal ──────────
            $users = $isAdmin ? \App\Models\User::orderBy('name')->get(['id', 'name', 'email']) : collect();
        
            $companies = Company::active()->with('rates')->get();
            session(['quotations_last_viewed_at' => now()]);
            return view('quotations.index', compact('quotations', 'analytics', 'users', 'companies'));
        }

    /**
     * Build a zero-filled daily trend for the last 14 days.
     */
    protected function buildTrend($baseQuery)
    {
        $dbDriver = DB::connection()->getDriverName();
        $dateExpr = $dbDriver === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at) as day"
            : "DATE(created_at) as day";

        // Callers hand us the same builder they paginate with, so it can already
        // carry an ORDER BY created_at (from ->latest()) and a LIMIT/OFFSET (from
        // ->paginate()). Both are wrong for an aggregate: MySQL rejects ordering
        // by a non-grouped column under only_full_group_by, and an inherited LIMIT
        // would silently truncate the trend. Strip both before grouping.
        $trendQuery = (clone $baseQuery)->reorder();
        $trendQuery->getQuery()->limit = null;
        $trendQuery->getQuery()->offset = null;

        $rows = $trendQuery
            ->select(DB::raw($dateExpr), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $counts = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('d M');
            $counts[] = (int) ($rows[$day->toDateString()]->count ?? 0);
        }

        return ['labels' => $labels, 'counts' => $counts];
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        QuoteSession::updateOrCreate(
            ['user_id' => $user->id, 'status' => 'started'],
            [
                'started_at' => now(),
                'expires_at' => now()->addHours(2),
            ]
        );

        $signedToken = Crypt::encrypt([
            'user_id' => auth()->id(),
            'expires' => now()->addHours(2)->timestamp,
        ]);
        $companies = Company::active()->get();
        $rates = Rate::whereIn('company_id', $companies->pluck('id'))
            ->where('category', 'Cover Limits')
            ->get()
            ->groupBy('company_id');

        $users = $isAdmin ? User::orderBy('name')->get(['id', 'name', 'email', 'first_name', 'last_name', 'mobile']) : collect();
        $targetUser = null;
        if ($isAdmin && request('on_behalf_of')) {
            $targetUser = User::find(request('on_behalf_of'));
        }

        $prefill = $this->buildPrefill($targetUser ?? $user);

        return view('quotations.create', compact('companies', 'rates', 'signedToken', 'users', 'targetUser', 'prefill'));
    }

    /**
     * Prefill personal + vehicle fields from the user's profile and latest quotation.
     */
    protected function buildPrefill(?User $profileUser)
    {
        $profileUser = $profileUser ?? auth()->user();

        $latest = $profileUser->quotations()->latest()->first();

        return [
            'salutation'   => $latest->salutation ?? '',
            'first_name'   => $profileUser->first_name ?? '',
            'middle_name'  => $latest->middle_name ?? '',
            'last_name'    => $profileUser->last_name ?? '',
            'phone'        => $profileUser->mobile ?? '',
            'email'        => $profileUser->email ?? '',
            'reg'          => $latest->car_registration ?? '',
            'chassis'      => $latest->chassis_number ?? '',
            'engine'       => $latest->engine_number ?? '',
            'make'         => $latest->car_make ?? '',
            'model'        => $latest->car_model ?? '',
            'body'         => $latest->body_type ?? '',
            'year'         => $latest->car_year ?? '',
            'color'        => $latest->color ?? '',
            'value'        => $latest ? (string) $latest->car_valuation : '',
            'vehicle_type' => $latest->vehicle_type ?? 'private',
        ];
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:2000|max:2030',
            'car_registration' => 'nullable|string|max:50',
            'car_valuation' => 'required|numeric|min:500000|max:10000000',
            'has_anti_theft' => 'boolean',
            'is_young_driver' => 'boolean',
            'include_pvt' => 'boolean',
            'vehicle_type' => 'sometimes|string|in:psv,private',
            'num_passengers' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vehicleType = $request->input('vehicle_type', 'psv');
        $numPassengers = $request->input('num_passengers', 1);

        $companies = Company::active()->get();
        $results = [];

        foreach ($companies as $company) {
            $results[] = $this->premiumCalculator->calculate(
                $company,
                $request->car_valuation,
                $vehicleType,
                $request->boolean('include_pvt', true),
                $numPassengers,
                $request->boolean('has_anti_theft', true),
                $request->boolean('is_young_driver', false)
            );
        }

        return response()->json(['results' => $results]);
    }

    public function store(Request $request)
    {
        try {
            $token = Crypt::decrypt($request->input('_signed'));
            if (!isset($token['user_id']) || $token['user_id'] !== (int) auth()->id() || !isset($token['expires']) || $token['expires'] < now()->timestamp) {
                return back()->withErrors(['_signed' => 'Invalid or expired form token. Please try again.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['_signed' => 'Invalid form token. Please try again.'])->withInput();
        }

        QuoteSession::where('user_id', auth()->id())
            ->where('status', 'started')
            ->update(['status' => 'completed', 'completed_at' => now()]);

        $validator = Validator::make($request->all(), [
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:2000|max:2030',
            'car_registration' => 'nullable|string|max:50',
            'car_valuation' => 'required|numeric|min:500000|max:10000000',
            'company_id' => 'required|exists:companies,id',
            'premium' => 'required|numeric',
            'pvt_premium' => 'nullable|numeric',
            'total_premium' => 'required|numeric',
            'excess_own_damage' => 'nullable|numeric',
            'excess_theft' => 'nullable|numeric',
            'on_behalf_of' => 'nullable|exists:users,id',
            'is_new_customer' => 'sometimes|boolean',
            'vehicle_type' => 'sometimes|string|in:psv,private',
            'num_passengers' => 'sometimes|integer|min:1',
            'salutation' => 'nullable|string|max:20',
            'first_name' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->boolean('is_new_customer'))],
            'middle_name' => 'nullable|string|max:255',
            'last_name' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->boolean('is_new_customer'))],
            'phone' => 'nullable|string|max:50',
            'email' => ['nullable', 'email', 'max:255', Rule::requiredIf($request->boolean('is_new_customer'))],
            'chassis_number' => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
            'body_type' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        $isNewCustomer = $request->boolean('is_new_customer');
        $targetUserId = $isNewCustomer
            ? null
            : ($isAdmin && $request->filled('on_behalf_of') ? (int) $request->on_behalf_of : auth()->id());

        $company = \App\Models\Company::find($request->company_id);
        $targetUserForSnapshot = User::find($targetUserId);

        // Recompute the premium server-side via PremiumCalculationService — never trust the
        // client-submitted premium/total_premium/breakdown fields, which come from a JS preview
        // calculation that can drift from the backend formula (e.g. the 2.5M valuation tier) and
        // can be tampered with in the browser before submit.
        $breakdown = $this->premiumCalculator->calculate(
            $company,
            $request->car_valuation,
            $request->input('vehicle_type', 'psv'),
            true,
            $request->input('num_passengers', 1),
            true,
            false
        );

        $quotation = DB::transaction(function () use ($request, $company, $targetUserId, $targetUserForSnapshot, $breakdown) {
            $quotation = Quotation::create([
                'user_id' => $targetUserId,
                'salutation' => $request->salutation,
                'first_name' => $request->filled('first_name') ? $request->first_name : ($targetUserForSnapshot->first_name ?? null),
                'middle_name' => $request->middle_name,
                'last_name' => $request->filled('last_name') ? $request->last_name : ($targetUserForSnapshot->last_name ?? null),
                'phone' => $request->filled('phone') ? $request->phone : ($targetUserForSnapshot->mobile ?? null),
                'email' => $request->filled('email') ? $request->email : ($targetUserForSnapshot->email ?? null),
                'company_id' => $request->company_id,
                'quotation_number' => Quotation::generateNumber($company),
                'car_make' => $request->car_make,
                'car_model' => $request->car_model,
                'car_year' => $request->car_year,
                'car_registration' => $request->car_registration,
                'chassis_number' => $request->chassis_number,
                'engine_number' => $request->engine_number,
                'body_type' => $request->body_type,
                'color' => $request->color,
                'car_valuation' => $request->car_valuation,
                'vehicle_type' => $request->input('vehicle_type', 'psv'),
                'num_passengers' => $request->input('num_passengers', 1),
                'premium' => $breakdown['basic_premium'],
                'pvt_premium' => $breakdown['pvt_premium'],
                'total_premium' => $breakdown['total_premium'],
                'excess_own_damage' => $breakdown['excess_own_damage'],
                'excess_theft' => $breakdown['excess_theft'],
                'status' => 'generated',
                'breakdown' => $breakdown,
            ]);

            AuditService::log('create', 'Quotation', $quotation->id, null, $quotation->toArray());
            AuditService::activity('quotation_generated', "Quotation {$quotation->quotation_number} generated");

            return $quotation;
        });

        $targetUser = User::find($targetUserId);
        $recipientEmail = $targetUser->email ?? $quotation->customer_email;
        if ($recipientEmail) {
            try {
                $mailable = new QuotationMail($quotation);
                Mail::to($recipientEmail)->send($mailable);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send quotation email: " . $e->getMessage());
            }
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new QuotationStatusChanged($quotation, 'new', 'generated'));
            try {
                Mail::to($admin->email)->send(new \App\Mail\QuotationCreatedAdminMail($quotation, $admin));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to email admin {$admin->email} about quotation {$quotation->quotation_number}: " . $e->getMessage());
            }
        }
        try {
            $quotation->user?->notify(new QuotationStatusChanged($quotation, 'new', 'generated'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to notify quotation owner: " . $e->getMessage());
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation generated successfully!');
    }

    public function show(Request $request, Quotation $quotation)
    {
        $this->authorizeView($quotation);
        $quotation->load(['user', 'company', 'items', 'stickers']);

        $showSuccess = $request->query('generated') === '1';

        $recentQuotations = collect();
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin')) {
            $recentQuotations = auth()->user()->quotations()
                ->with(['user', 'company'])
                ->latest()
                ->take(10)
                ->get();
        }

        $breakdown = $this->quotationBreakdown($quotation);
        $rateCategories = $this->quotationRateCategories($quotation);

        return view('quotations.show', compact('quotation', 'recentQuotations', 'showSuccess', 'breakdown', 'rateCategories'));
    }

    /**
     * Group a quotation's company rate settings into the standard quotation
     * document sections, pulled as-is from the Rate Settings so the web page
     * and PDF always show the same underwriter-configured benefits/limits.
     */
    protected function quotationRateCategories(Quotation $quotation): array
    {
        $company = $quotation->company;
        if (!$company) {
            return [];
        }

        $rates = $company->rates()
            ->where('vehicle_type', $quotation->vehicle_type ?? 'private')
            ->get()
            ->groupBy('category');

        // Any rate item whose notes literally say it isn't covered is dropped —
        // it's noise, not information the customer needs to see.
        $notCovered = fn($r) => $r->notes && stripos($r->notes, 'no cover') !== false;

        return [
            'BENEFITS / COVER LIMITS' => $rates->get('Cover Limits', collect())
                ->merge($rates->get('Extras', collect()))
                ->reject(fn($r) => $r->item_name === 'Geographical Area')
                ->reject($notCovered),
            'THIRD PARTY LIMITS OF LIABILITY' => $rates->get('Third Party Limits', collect())->reject($notCovered),
            'POLICY EXCESSES' => $rates->get('Excess', collect())->reject($notCovered),
            'SPECIAL CONDITIONS' => $rates->get('Additional Cover', collect())->reject($notCovered),
            'ADDITIONAL INFO' => $rates->get('Optional Benefits', collect())
                ->merge($rates->get('Premium Rate', collect())->whereIn('item_name', ['Max Age New Business', 'Minimum Sum Insured']))
                ->reject($notCovered),
        ];
    }

    /**
     * Recompute a quotation's premium breakdown fresh from the current
     * company rate settings, so the detail page always reflects the real
     * backend calculation rather than a value frozen at creation time.
     */
    protected function quotationBreakdown(Quotation $quotation): array
    {
        $company = $quotation->company;
        if (!$company) {
            return [];
        }

        $breakdown = $this->premiumCalculator->calculate(
            $company,
            (float) $quotation->car_valuation,
            $quotation->vehicle_type ?? 'private',
            true,
            $quotation->num_passengers ?? 1,
            true,
            false
        );

        $geoRate = Rate::where('company_id', $company->id)
            ->where('vehicle_type', $quotation->vehicle_type ?? 'private')
            ->where('item_name', 'Geographical Area')
            ->first();
        $breakdown['geo_limit'] = $geoRate?->notes
            ?: ($geoRate?->value ? 'KES ' . number_format($geoRate->value, 0) : 'Kenya');

        return $breakdown;
    }

    public function downloadPdf(Quotation $quotation)
    {
        $this->authorizeView($quotation);
        $quotation->load(['user', 'company']);
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
        $filename = 'quotation-' . str_replace(['/', '\\'], '-', $quotation->quotation_number) . '.pdf';
        return $pdf->download($filename);
    }

    public function previewPdf(Quotation $quotation)
    {
        $this->authorizeView($quotation);
        $quotation->load(['user', 'company']);
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
        $filename = 'quotation-' . str_replace(['/', '\\'], '-', $quotation->quotation_number) . '.pdf';
        return $pdf->stream($filename);
    }

    public function publicDownloadPdf(Quotation $quotation)
    {
        $this->authorizeView($quotation);

        $quotation->load(['user', 'company']);
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
        $filename = 'quotation-' . str_replace(['/', '\\'], '-', $quotation->quotation_number) . '.pdf';
        return $pdf->download($filename);
    }

    public function destroy(Quotation $quotation)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }
        $quotation->delete();

        AuditService::log('delete', 'Quotation', $quotation->id, $quotation->toArray(), null);
        AuditService::activity('quotation_deleted', "Quotation {$quotation->quotation_number} deleted");

        return redirect()->route('quotations.index')->with('success', 'Quotation hidden successfully.');
    }

    public function restore($id)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }
        $quotation = Quotation::withTrashed()->findOrFail($id);
        $quotation->restore();

        AuditService::activity('quotation_restored', "Quotation {$quotation->quotation_number} restored");

        return redirect()->route('quotations.index')->with('success', 'Quotation restored successfully.');
    }

    protected function authorizeView(Quotation $quotation)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            abort(403);
        }
    }

    public function myQuote(Request $request)
    {
        $user = auth()->user();
        $quotation = $user->quotations()->with(['user', 'company', 'items', 'stickers'])->latest()->firstOrFail();
        $showSuccess = $request->query('generated') === '1';

        $recentQuotations = $user->quotations()
            ->with(['user', 'company'])
            ->latest()
            ->take(10)
            ->get();

        $breakdown = $this->quotationBreakdown($quotation);
        $rateCategories = $this->quotationRateCategories($quotation);

        return view('quotations.show', compact('quotation', 'showSuccess', 'recentQuotations', 'breakdown', 'rateCategories'));
    }

    public function myQuotations()
    {
        $user = auth()->user();

        $query = Quotation::with(['user', 'company'])
            ->where('user_id', $user->id)
            ->latest();

        $quotations = $query->paginate(15)->withQueryString();

        $analytics = [
            'total'      => (clone $query)->count(),
            'today'      => (clone $query)->whereDate('created_at', today())->count(),
            'this_week'  => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'trend'      => $this->buildTrend($query),
        ];

        return view('quotations.mine', compact('quotations', 'analytics'));
    }

    public function recentQuotations()
    {
        $user = auth()->user();

        $quotations = $user->quotations()
            ->with(['user', 'company'])
            ->latest()
            ->take(10)
            ->get();

        return view('quotations.recent', compact('quotations'));
    }

    public function preview(Request $request)
    {
        $companies = Company::active()->with('rates')->get();

        $buildCompanyData = function ($companies, $vehicleType) {
            return $companies->filter(fn($c) => in_array($c->company_type, ['both', $vehicleType]))
                ->values()
                ->map(function ($c) use ($vehicleType) {
                    $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
                    $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
                    $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
                    $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
                    return [
                        'id' => $c->id,
                        'slug' => $c->slug,
                        'name' => $c->name,
                        'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                        'minPremium' => $rate ? (float) $rate->min_premium : 0,
                        'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                        'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                        'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                        'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                        'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                        'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
                    ];
                });
        };

        $companiesPrivate = $buildCompanyData($companies, 'private');
        $companiesPsv = $buildCompanyData($companies, 'psv');

        $targetUser = null;
        $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        if ($isAdmin && $request->filled('on_behalf_of')) {
            $targetUser = User::find($request->on_behalf_of);
        }

        return view('quotations.preview', compact('companiesPrivate', 'companiesPsv', 'targetUser'));
    }

    /**
     * "Compare All" page — shown when the user picks Compare All while
     * generating a quote. Reads the vehicle/personal details already
     * collected client-side (sessionStorage) and, via compareAllCalculate(),
     * computes each matching company's premium using the real backend
     * PremiumCalculationService, not a client-side formula.
     */
    public function compareAll(Request $request)
    {
        $targetUser = null;
        $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        if ($isAdmin && $request->filled('on_behalf_of')) {
            $targetUser = User::find($request->on_behalf_of);
        }

        return view('quotations.comparisson-show', compact('targetUser'));
    }

    public function compareAllCalculate(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|string|in:psv,private',
            'car_valuation' => 'required|numeric|min:0',
            'num_passengers' => 'sometimes|integer|min:1',
        ]);

        $vehicleType = $request->vehicle_type;
        $valuation = (float) $request->car_valuation;
        $numPassengers = $request->input('num_passengers', 1);

        $companies = Company::active()
            ->with('rates')
            ->get()
            ->filter(fn($c) => in_array($c->company_type, ['both', $vehicleType]))
            ->values();

        $results = $companies->map(function ($company) use ($vehicleType, $valuation, $numPassengers) {
            $breakdown = $this->premiumCalculator->calculate(
                $company,
                $valuation,
                $vehicleType,
                true,
                $numPassengers,
                true,
                false
            );

            $companyRates = $company->rates->where('vehicle_type', $vehicleType);
            $geoRate = $companyRates->firstWhere('item_name', 'Geographical Area');
            $excessProtectorRate = $companyRates->firstWhere('item_name', 'Excess Protector');
            $maxAgeRate = $companyRates->firstWhere('item_name', 'Max Age New Business');
            $minSumInsured = $vehicleType === 'psv' ? (float) $company->min_sum_insured_psv : (float) $company->min_sum_insured_private;
            $minSumInsuredRate = $companyRates->firstWhere('item_name', 'Minimum Sum Insured');

            $breakdown['geo_limit'] = $geoRate?->notes
                ?: ($geoRate?->value ? 'KES ' . number_format($geoRate->value, 0) : 'Kenya');

            return [
                'company' => [
                    'id' => $company->id,
                    'slug' => $company->slug,
                    'name' => $company->name,
                    'logo' => $company->logoUrl(),
                ],
                'breakdown' => $breakdown,
                'benefits' => [
                    'excess_protector' => $excessProtectorRate?->notes,
                    'max_age' => $maxAgeRate ? $maxAgeRate->value . ' years' : null,
                    'min_sum_insured' => $minSumInsured ? 'KES ' . number_format($minSumInsured, 0) : ($minSumInsuredRate?->notes ?: null),
                ],
            ];
        })->values();

        return response()->json(['results' => $results]);
    }

    /**
     * Builds one combined PDF containing every matching underwriter's full
     * quotation document (same layout as a single quotation download, one
     * per company, paginated back to back) so "Compare All" can be
     * previewed, downloaded, or printed as a single file. Mirrors
     * compareAllCalculate()'s company matching + premium calculation, but
     * renders unsaved Quotation instances (never persisted) through the
     * existing pdf.partials.quotation-body template.
     */
    protected function buildCompareAllQuotations(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|string|in:psv,private',
            'car_valuation' => 'required|numeric|min:0',
            'num_passengers' => 'sometimes|integer|min:1',
            'salutation' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'car_registration' => 'nullable|string|max:30',
            'chassis_number' => 'nullable|string|max:60',
            'engine_number' => 'nullable|string|max:60',
            'car_make' => 'nullable|string|max:100',
            'car_model' => 'nullable|string|max:100',
            'body_type' => 'nullable|string|max:100',
            'car_year' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
        ]);

        $vehicleType = $request->vehicle_type;
        $valuation = (float) $request->car_valuation;
        $numPassengers = (int) $request->input('num_passengers', 1);

        $companies = Company::active()
            ->with('rates')
            ->get()
            ->filter(fn($c) => in_array($c->company_type, ['both', $vehicleType]))
            ->values();

        return $companies->map(function ($company) use ($request, $vehicleType, $valuation, $numPassengers) {
            $breakdown = $this->premiumCalculator->calculate(
                $company,
                $valuation,
                $vehicleType,
                true,
                $numPassengers,
                true,
                false
            );

            $quotation = new Quotation($request->only([
                'salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'email',
                'car_make', 'car_model', 'car_year', 'car_registration', 'chassis_number',
                'engine_number', 'body_type', 'color',
            ]));
            $quotation->car_valuation = $valuation;
            $quotation->vehicle_type = $vehicleType;
            $quotation->num_passengers = $numPassengers;
            $quotation->premium = $breakdown['basic_premium'] ?? 0;
            $quotation->pvt_premium = $breakdown['pvt_premium'] ?? 0;
            $quotation->total_premium = $breakdown['total_premium'] ?? 0;
            $quotation->breakdown = $breakdown;
            $quotation->quotation_number = 'CMP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper($company->slug);
            $quotation->setRelation('company', $company);

            return $quotation;
        })->values();
    }

    public function compareAllPdfPreview(Request $request)
    {
        $quotations = $this->buildCompareAllQuotations($request);
        $pdf = Pdf::loadView('pdf.compare-all-quotations', ['quotations' => $quotations]);
        return $pdf->stream('compare-all-insurers-' . now()->format('Ymd-His') . '.pdf');
    }

    public function compareAllPdfDownload(Request $request)
    {
        $quotations = $this->buildCompareAllQuotations($request);
        $pdf = Pdf::loadView('pdf.compare-all-quotations', ['quotations' => $quotations]);
        return $pdf->download('compare-all-insurers-' . now()->format('Ymd-His') . '.pdf');
    }

    public function regenerate(Request $request)
    {
        $registrationNumber = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'registration_number' => 'required|string|max:50',
            ]);

            $registrationNumber = strtoupper(trim($request->registration_number));
        } elseif ($request->isMethod('get') && session()->has('regenerate_registration')) {
            $registrationNumber = session()->pull('regenerate_registration');
        } else {
            return redirect()->route('home')->with('error', 'Invalid request.');
        }

        $existingQuotation = Quotation::whereEqualsCI('car_registration', $registrationNumber)
            ->with(['company', 'items'])
            ->latest()
            ->first();

        if (!$existingQuotation) {
            return redirect()->route('home')->with('error', 'No quotation found for vehicle registration ' . $registrationNumber . '. Please generate a new quote.');
        }

        if (!auth()->check()) {
            session(['regenerate_registration' => $registrationNumber]);
            return redirect()->route('login')->with('info', 'Please log in to regenerate your quote.');
        }

        $user = auth()->user();

        $newQuotation = DB::transaction(function () use ($existingQuotation, $user) {
            $company = $existingQuotation->company;
            $quotation = Quotation::create([
                'user_id' => $user->id,
                'salutation' => $existingQuotation->salutation,
                'first_name' => $existingQuotation->first_name,
                'middle_name' => $existingQuotation->middle_name,
                'last_name' => $existingQuotation->last_name,
                'phone' => $existingQuotation->phone,
                'email' => $existingQuotation->email,
                'company_id' => $company->id,
                'quotation_number' => Quotation::generateNumber($company),
                'car_make' => $existingQuotation->car_make,
                'car_model' => $existingQuotation->car_model,
                'car_year' => $existingQuotation->car_year,
                'car_registration' => $existingQuotation->car_registration,
                'chassis_number' => $existingQuotation->chassis_number,
                'engine_number' => $existingQuotation->engine_number,
                'body_type' => $existingQuotation->body_type,
                'color' => $existingQuotation->color,
                'car_valuation' => $existingQuotation->car_valuation,
                'vehicle_type' => $existingQuotation->vehicle_type,
                'num_passengers' => $existingQuotation->num_passengers,
                'premium' => $existingQuotation->premium,
                'pvt_premium' => $existingQuotation->pvt_premium,
                'total_premium' => $existingQuotation->total_premium,
                'excess_own_damage' => $existingQuotation->excess_own_damage,
                'excess_theft' => $existingQuotation->excess_theft,
                'status' => 'generated',
                'breakdown' => $existingQuotation->breakdown,
            ]);

            foreach ($existingQuotation->items as $item) {
                $quotation->items()->create([
                    'item_name' => $item->item_name,
                    'coverage' => $item->coverage,
                    'limit_amount' => $item->limit_amount,
                    'notes' => $item->notes,
                ]);
            }

            return $quotation;
        });

        $user->notify(new \App\Notifications\QuotationStatusChanged($newQuotation, 'regenerated', 'generated'));

        return redirect()->route('my.quote', ['generated' => 1])
            ->with('success', 'A quote for vehicle registration ' . $registrationNumber . ' has been found. Regenerating the quote.');
    }
}
