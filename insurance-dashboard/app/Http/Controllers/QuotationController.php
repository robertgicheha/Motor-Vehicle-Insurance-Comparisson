<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Company;
use App\Models\Rate;
use App\Services\PremiumCalculationService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    protected $premiumCalculator;

    public function __construct(PremiumCalculationService $premiumCalculator)
    {
        $this->premiumCalculator = $premiumCalculator;
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $quotations = Quotation::with(['user', 'company'])->latest()->paginate(15);
            return view('quotations.index', compact('quotations'));
        }
        $quotations = $user->quotations()->with('company')->latest()->paginate(15);
        return view('quotations.index', compact('quotations'));
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

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation generated successfully!');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeView($quotation);
        $quotation->load(['user', 'company', 'items']);
        return view('quotations.show', compact('quotation'));
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
}
