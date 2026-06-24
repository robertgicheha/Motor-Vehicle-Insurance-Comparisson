<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Models\Quotation;
use App\Models\Company;
use App\Models\Rate;
use App\Services\PremiumCalculationService;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
        
            $analytics = [
                'total'      => (clone $analyticsBase)->count(),
                'today'      => (clone $analyticsBase)->whereDate('created_at', today())->count(),
                'this_week'  => (clone $analyticsBase)->whereBetween('created_at', [
                                    now()->startOfWeek(), now()->endOfWeek()
                                ])->count(),
                'this_month' => (clone $analyticsBase)->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->count(),
        
                // Quotations grouped by company (for bar + doughnut charts)
                'by_company' => (clone $analyticsBase)
                    ->join('companies', 'quotations.company_id', '=', 'companies.id')
                    ->selectRaw('companies.name as company, COUNT(*) as count, SUM(total_premium) as total_premium')
                    ->groupBy('companies.name')
                    ->orderByDesc('count')
                    ->get()
                    ->toArray(),
        
                // Daily counts for last 14 days (trend line)
                'trend' => (clone $analyticsBase)
                    ->selectRaw("DATE(created_at) as date, COUNT(*) as count")
                    ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                    ->groupByRaw("DATE(created_at)")
                    ->orderBy('date')
                    ->get()
                    ->map(fn($r) => [
                        'date'  => \Carbon\Carbon::parse($r->date)->format('d M'),
                        'count' => $r->count,
                    ])
                    ->toArray(),
            ];
        
            // ── Users list for admin "Apply for User" modal ──────────
            $users = $isAdmin ? \App\Models\User::orderBy('name')->get(['id', 'name', 'email']) : collect();
        
            return view('quotations.index', compact('quotations', 'analytics', 'users'));
        }

    public function create()
    {
        $companies = Company::active()->get();
        $rates = Rate::whereIn('company_id', $companies->pluck('id'))
            ->where('category', 'Cover Limits')
            ->get()
            ->groupBy('company_id');
        return view('quotations.create', compact('companies', 'rates'));
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
                $request->boolean('include_pvt', true),
                $request->boolean('has_anti_theft', true),
                $request->boolean('is_young_driver', false)
            );
        }

        return response()->json(['results' => $results]);
    }

    public function store(Request $request)
    {
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quotation = DB::transaction(function () use ($request) {
            $quotation = Quotation::create([
                'user_id' => auth()->id(),
                'company_id' => $request->company_id,
                'quotation_number' => Quotation::generateNumber(),
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
                'status' => 'generated',
                'breakdown' => $request->breakdown ?? [],
            ]);

            AuditService::log('create', 'Quotation', $quotation->id, null, $quotation->toArray());
            AuditService::activity('quotation_generated', "Quotation {$quotation->quotation_number} generated");

            return $quotation;
        });

        $user = auth()->user();
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new QuotationMail($quotation));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send quotation email: " . $e->getMessage());
            }
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
        $this->authorizeView($quotation);
        $quotation->delete();

        AuditService::log('delete', 'Quotation', $quotation->id, $quotation->toArray(), null);
        AuditService::activity('quotation_deleted', "Quotation {$quotation->quotation_number} deleted");

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    protected function authorizeView(Quotation $quotation)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            abort(403);
        }
    }

    public function preview(Request $request)
    {
        return view('quotations.preview');
    }
}
