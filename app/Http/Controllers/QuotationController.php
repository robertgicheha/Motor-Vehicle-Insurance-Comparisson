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
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->latest();

        $quotations = $query->paginate(20);

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'expired' => (clone $query)->where('status', 'expired')->count(),
            'processing' => (clone $query)->where('status', 'processing')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'closed' => (clone $query)->where('status', 'closed')->count(),
            'insuring_sticker' => (clone $query)->where('status', 'insuring_sticker')->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'generated' => (clone $query)->where('status', 'generated')->count(),
        ];

        return view('quotations.tracker', compact('quotations', 'stats'));
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $this->authorizeView($quotation);

        $request->validate(['status' => 'required|string']);

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
            $quotation->user->notify(new QuotationStatusChanged($quotation, $oldStatus, $request->status));
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
        
            $dbDriver = DB::connection()->getDriverName();
            $dateExpr = $dbDriver === 'sqlite'
                ? "strftime('%d %b', created_at) as date"
                : "DATE_FORMAT(created_at, '%d %b') as date";

            $analytics = [
            'total'      => Quotation::count(),
            'today'      => Quotation::whereDate('created_at', today())->count(),
            'this_week'  => Quotation::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Quotation::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->count(),

            // Daily trend — last 14 days
            'trend' => Quotation::select(
                            DB::raw($dateExpr),
                            DB::raw('COUNT(*) as count')
                        )
                        ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                        ->groupBy('date', DB::raw('DATE(created_at)'))
                        ->orderBy(DB::raw('DATE(created_at)'))
                        ->get(),

            // Per-company quotation count + total premium — for both charts
            'by_company' => Company::withCount('quotations')
                        ->withSum('quotations', 'total_premium')
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

        return view('quotations.create', compact('companies', 'rates', 'signedToken', 'users', 'targetUser'));
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:2000|max:2030',
            'car_registration' => 'nullable|string|max:50',
            'car_valuation' => 'required|numeric|min:800000|max:1300000',
            'has_anti_theft' => 'boolean',
            'is_young_driver' => 'boolean',
            'include_pvt' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $companies = Company::active()->get();
        $results = [];

        foreach ($companies as $company) {
            $results[] = $this->premiumCalculator->calculate(
                $company,
                $request->car_valuation,
                $request->boolean('include_pvt', false),
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
            'car_valuation' => 'required|numeric|min:800000|max:1300000',
            'company_id' => 'required|exists:companies,id',
            'premium' => 'required|numeric',
            'pvt_premium' => 'nullable|numeric',
            'total_premium' => 'required|numeric',
            'excess_own_damage' => 'nullable|numeric',
            'excess_theft' => 'nullable|numeric',
            'on_behalf_of' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        $targetUserId = $isAdmin && $request->filled('on_behalf_of')
            ? (int) $request->on_behalf_of
            : auth()->id();

        $company = \App\Models\Company::find($request->company_id);
        $quotation = DB::transaction(function () use ($request, $company, $targetUserId) {
            $quotation = Quotation::create([
                'user_id' => $targetUserId,
                'company_id' => $request->company_id,
                'quotation_number' => Quotation::generateNumber($company),
                'car_make' => $request->car_make,
                'car_model' => $request->car_model,
                'car_year' => $request->car_year,
                'car_registration' => $request->car_registration,
                'car_valuation' => $request->car_valuation,
                'premium' => $request->premium,
                'pvt_premium' => $request->pvt_premium ?? 0,
                'total_premium' => $request->total_premium,
                'excess_own_damage' => $request->excess_own_damage,
                'excess_theft' => $request->excess_theft,
                'status' => 'processing',
                'breakdown' => $request->breakdown ?? [],
            ]);

            AuditService::log('create', 'Quotation', $quotation->id, null, $quotation->toArray());
            AuditService::activity('quotation_generated', "Quotation {$quotation->quotation_number} generated");

            return $quotation;
        });

        $targetUser = User::find($targetUserId);
        if ($targetUser && $targetUser->email) {
            try {
                Mail::to($targetUser->email)->send(new QuotationMail($quotation));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send quotation email: " . $e->getMessage());
            }
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new QuotationStatusChanged($quotation, 'new', 'processing'));
        }
        try {
            $quotation->user->notify(new QuotationStatusChanged($quotation, 'new', 'processing'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to notify quotation owner: " . $e->getMessage());
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation generated successfully!');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeView($quotation);
        $quotation->load(['user', 'company', 'items']);
        return view('quotations.show', compact('quotation'));
    }

    public function downloadPdf(Quotation $quotation)
    {
        $this->authorizeView($quotation);
        $quotation->load(['user', 'company']);
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
        return $pdf->download("quotation-{$quotation->quotation_number}.pdf");
    }

    public function publicDownloadPdf(Quotation $quotation)
    {
        $quotation->load(['user', 'company']);
        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
        return $pdf->download("quotation-{$quotation->quotation_number}.pdf");
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
        $quotation = $user->quotations()->with(['user', 'company', 'items'])->latest()->firstOrFail();
        $showSuccess = $request->query('generated') === '1';
        return view('quotations.show', compact('quotation', 'showSuccess'));
    }

    public function preview(Request $request)
    {
        $companies = Company::active()->with('rates')->get()->map(function ($c) {
            $rate = $c->rates->firstWhere('item_name', 'Premium Rate');
            $iraLevy = $c->rates->firstWhere('item_name', 'IRA Levy');
            return [
                'id' => $c->id,
                'slug' => $c->slug,
                'name' => $c->name,
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'taxRate' => $iraLevy ? (float) $iraLevy->percentage / 100 : 0.005,
            ];
        })->values();

        $targetUser = null;
        $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        if ($isAdmin && $request->filled('on_behalf_of')) {
            $targetUser = User::find($request->on_behalf_of);
        }

        return view('quotations.preview', compact('companies', 'targetUser'));
    }
}
