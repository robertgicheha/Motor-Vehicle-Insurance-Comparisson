<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Mail\StickerMail;
use App\Models\Company;
use App\Models\Quotation;
use App\Models\Rate;
use App\Models\Sticker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class PublicController extends Controller
{
    public function motorQuote()
    {
        $companies = Company::active()->with('rates')->get();
        $coverage = $this->getCoverageData($companies);
        $companyStats = Quotation::selectRaw('company_id, COUNT(*) as total, SUM(total_premium) as total_premium')
            ->whereIn('company_id', $companies->pluck('id'))
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');
        return view('motor-quote', compact('companies', 'coverage', 'companyStats'));
    }

    public function motorPrivate()
    {
        $companies = Company::active()->with('rates')->get();
        $vehicleType = 'private';
        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
        $companyData = $companies->filter(fn($c) => in_array($c->company_type, ['private', 'both']))->map(function ($c, $i) use ($companyColors, $vehicleType) {
            $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
            $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
            $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'rate_display' => $rate ? ((float)$rate->percentage) . '%' : '—',
                'minPremium' => $rate ? (float) $rate->min_premium : 0,
                'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                'color' => $companyColors[$i % count($companyColors)],
                'prefix' => $c->getInitials(),
            ];
        })->values();
        $companyStats = Quotation::selectRaw('company_id, COUNT(*) as total, SUM(total_premium) as total_premium')
            ->whereIn('company_id', $companies->pluck('id'))
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');
        return view('motor-private', compact('companies', 'companyData', 'companyStats'));
    }

    public function motorPsv()
    {
        $companies = Company::active()->with('rates')->get();
        $vehicleType = 'psv';
        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
        $companyData = $companies->filter(fn($c) => in_array($c->company_type, ['psv', 'both']))->map(function ($c, $i) use ($companyColors, $vehicleType) {
            $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
            $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
            $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'rate_display' => $rate ? ((float)$rate->percentage) . '%' : '—',
                'minPremium' => $rate ? (float) $rate->min_premium : 0,
                'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                'color' => $companyColors[$i % count($companyColors)],
                'prefix' => $c->getInitials(),
            ];
        })->values();
        $companyStats = Quotation::selectRaw('company_id, COUNT(*) as total, SUM(total_premium) as total_premium')
            ->whereIn('company_id', $companies->pluck('id'))
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');
        return view('motor-psv', compact('companies', 'companyData', 'companyStats'));
    }

    public function quotation(?string $company = null)
    {
        $vehicleType = request()->query('vehicle_type', 'psv');
        $numPassengers = request()->query('num_passengers', 1);
        $companies = Company::active()->with('rates')->get();
        $selectedCompany = null;

        if ($company) {
            $selectedCompany = $companies->firstWhere('slug', $company);
        }

        $companiesForType = $companies->filter(fn($c) => in_array($c->company_type, ['both', $vehicleType]));
        $coverage = $this->getCoverageData($companiesForType);

        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
        $companyData = $companiesForType->values()->map(function ($c, $i) use ($companyColors, $vehicleType) {
            $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
            $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
            $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
            $maxAgeRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Max Age New Business');
            $excessProtectorRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Excess Protector');
            $geoAreaRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Geographical Area');
            $minSumInsuredRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Minimum Sum Insured');
            $minSumInsured = $vehicleType === 'psv' ? (float) $c->min_sum_insured_psv : (float) $c->min_sum_insured_private;
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'taxRate' => 0,
                'minPremium' => $rate ? (float) $rate->min_premium : 0,
                'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                'maxAge' => $maxAgeRate ? (int) $maxAgeRate->value : 99,
                'minSumInsured' => $minSumInsured,
                'minSumInsuredText' => $minSumInsured ? 'KES ' . number_format($minSumInsured, 0) : ($minSumInsuredRate?->notes ?? '—'),
                'maxAgeText' => $maxAgeRate ? $maxAgeRate->value . ' years' : '—',
                'excessProtector' => $excessProtectorRate?->notes ?? null,
                'geoArea' => $geoAreaRate?->notes ?? ($geoAreaRate?->value ? 'KES ' . number_format($geoAreaRate->value, 0) : null),
            ];
        })->values();

        $hardcodedLogos = [];
        foreach (Company::hardcodedLogoMap() as $slug => $path) {
            $hardcodedLogos[$slug] = asset($path);
        }

        // Logo shown in the "Compare All" box on generate-quote.blade.php (line 299).
        // Uses the same asset() pattern as $hardcodedLogos above.
        $__compareAllLogoUrl = asset('images/compare-all-logo.png');

        $userQuotation = null;
        if (auth()->check() && $selectedCompany) {
            $userQuotation = auth()->user()->quotations()
                ->where('company_id', $selectedCompany->id)
                ->latest()
                ->first();
        }

        $iraLevyRate = $selectedCompany?->rates->firstWhere('item_name', 'IRA Levy');
        $taxRate = $iraLevyRate ? (float) $iraLevyRate->percentage / 100 : 0.005;

        return view('quotation', compact('companies', 'selectedCompany', 'taxRate', 'coverage', 'companyData', 'userQuotation', 'hardcodedLogos', '__compareAllLogoUrl', 'vehicleType', 'numPassengers'));
    }

    public function lookupQuotation(Request $request)
    {
        $request->validate(['number' => 'required|string']);

        $quotation = Quotation::where('quotation_number', $request->number)->with(['company', 'user', 'items'])->first();

        if (!$quotation) {
            return back()->with('error', 'No quotation found with that number.');
        }

        return redirect()->route('public.quotation.show', $quotation->quotation_number);
    }

    public function lookupSticker(Request $request)
    {
        $request->validate(['number' => 'required|string']);

        $sticker = Sticker::where('sticker_number', $request->number)->with('company')->first();

        if (!$sticker) {
            return back()->with('error', 'No certificate found with that number.');
        }

        return redirect()->route('public.sticker.show', $sticker->sticker_number);
    }

    public function showQuotation($number)
    {
        $quotation = Quotation::where('quotation_number', $number)
            ->with(['company', 'items'])
            ->firstOrFail();

        return view('public.quotation-show', compact('quotation'));
    }

    public function showSticker($number)
    {
        $sticker = Sticker::where('sticker_number', $number)
            ->with('company')
            ->firstOrFail();

        return view('public.sticker-show', compact('sticker'));
    }

    public function downloadQuotationPdf($number)
    {
        $quotation = Quotation::where('quotation_number', $number)
            ->with(['user', 'company'])
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
        return $pdf->download("quotation-{$quotation->quotation_number}.pdf");
    }

    public function downloadStickerPdf($number)
    {
        $sticker = Sticker::where('sticker_number', $number)
            ->with('company')
            ->firstOrFail();

        $sticker->increment('download_count');
        $sticker->update(['last_downloaded_at' => now()]);

        $pdf = Pdf::loadView('pdf.sticker', ['sticker' => $sticker]);
        return $pdf->download("certificate-{$sticker->sticker_number}.pdf");
    }

    public function sendQuotationEmail(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'email' => 'required|email',
        ]);

        $quotation = Quotation::where('quotation_number', $request->number)
            ->with(['company', 'user'])
            ->firstOrFail();

        try {
            Mail::to($request->email)->send(new QuotationMail($quotation));
            return back()->with('success', 'Quotation sent to ' . $request->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email. Please try again.');
        }
    }

    public function sendStickerEmail(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'email' => 'required|email',
        ]);

        $sticker = Sticker::where('sticker_number', $request->number)
            ->with('company')
            ->firstOrFail();

        try {
            Mail::to($request->email)->send(new StickerMail($sticker));
            return back()->with('success', 'Certificate sent to ' . $request->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email. Please try again.');
        }
    }

    public function storeQuotationFromSession(Request $request)
    {
        $request->validate([
            'company_slug' => 'required|string|exists:companies,slug',
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer',
            'car_registration' => 'nullable|string|max:50',
            'car_valuation' => 'required|numeric|min:0',
            'vehicle_type' => 'sometimes|string|in:psv,private',
            'num_passengers' => 'sometimes|integer|min:1',
        ]);

        $company = Company::where('slug', $request->company_slug)->firstOrFail();
        $vehicleType = $request->input('vehicle_type', 'psv');
        $numPassengers = $request->input('num_passengers', 1);

        $calculator = app(\App\Services\PremiumCalculationService::class);
        $result = $calculator->calculate(
            $company,
            $request->car_valuation,
            $vehicleType,
            true,
            $numPassengers,
            true,
            false
        );

        $quotation = auth()->user()->quotations()->create([
            'company_id' => $company->id,
            'quotation_number' => Quotation::generateNumber($company),
            'car_make' => $request->car_make,
            'car_model' => $request->car_model,
            'car_year' => $request->car_year,
            'car_registration' => $request->car_registration,
            'car_valuation' => $request->car_valuation,
            'vehicle_type' => $vehicleType,
            'num_passengers' => $numPassengers,
            'premium' => $result['basic_premium'],
            'pvt_premium' => $result['pvt_premium'],
            'total_premium' => $result['total_premium'],
            'excess_own_damage' => $result['excess_own_damage'],
            'excess_theft' => $result['excess_theft'],
            'status' => 'generated',
            'breakdown' => $result,
        ]);

        return response()->json([
            'success' => true,
            'quotation' => [
                'id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'premium' => $basicPremium,
                'total_premium' => $total,
            ],
        ]);
    }

    public function globalSearch(Request $request)
    {
        $q = $request->q;
        if (!$q || strlen($q) < 2) {
            return response()->json(['quotations' => [], 'stickers' => []]);
        }

        $quotations = Quotation::where('quotation_number', 'like', "%{$q}%")
            ->orWhere('car_registration', 'like', "%{$q}%")
            ->orWhere('car_make', 'like', "%{$q}%")
            ->orWhere('car_model', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'quotation_number', 'car_make', 'car_model', 'car_registration']);

        $stickers = Sticker::where('sticker_number', 'like', "%{$q}%")
            ->orWhere('customer_name', 'like', "%{$q}%")
            ->orWhere('vehicle_registration', 'like', "%{$q}%")
            ->orWhere('car_make', 'like', "%{$q}%")
            ->orWhere('car_model', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'sticker_number', 'customer_name', 'car_make', 'car_model']);

        return response()->json([
            'quotations' => $quotations,
            'stickers' => $stickers,
        ]);
    }

    public function generateQuote(?string $company = null)
    {
        $vehicleType = request()->query('vehicle_type', 'psv');
        $numPassengers = request()->query('num_passengers', 1);
        $companies = Company::active()->with('rates')->get();
        $selectedCompany = null;
        $mode = 'all';

        if ($company && $company !== 'compare-all') {
            $selectedCompany = $companies->firstWhere('slug', $company);
            if ($selectedCompany) {
                $mode = 'single';
            }
        } elseif ($company === 'compare-all') {
            $mode = 'compare';
        }

        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B', '#7c3aed', '#0f7a65'];

        $buildData = function($companies, $vehicleType) use ($companyColors) {
            return $companies->filter(function($c) use ($vehicleType) {
                return in_array($c->company_type, ['both', $vehicleType]);
            })->values()->map(function ($c, $i) use ($companyColors, $vehicleType) {
                $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
                $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
                $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
                return [
                    'slug' => $c->slug,
                    'name' => $c->name,
                    'logo' => $c->logoUrl(),
                    'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                    'taxRate' => 0,
                    'minPremium' => $rate ? (float) $rate->min_premium : 0,
                    'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                    'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                    'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                    'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                    'color' => $companyColors[$i % count($companyColors)],
                    'prefix' => $c->getInitials(),
                ];
            });
        };

        $companyData = $buildData($companies, $vehicleType);
        $privateCompanyData = $buildData($companies, 'private');
        $psvCompanyData = $buildData($companies, 'psv');

        $minSumInsured = 0;
        $maxSumInsured = 10000000;
        if ($selectedCompany) {
            $minSumInsured = $vehicleType === 'private'
                ? (float)($selectedCompany->min_sum_insured_private ?? 500000)
                : (float)($selectedCompany->min_sum_insured_psv ?? 800000);
        }

        $coverage = $this->getCoverageData($companies);

        return view('public.generate-quote', compact(
            'companies', 'selectedCompany', 'mode', 'companyData', 'privateCompanyData', 'psvCompanyData',
            'coverage', 'vehicleType', 'minSumInsured', 'maxSumInsured'
        ));
    }

    private function getCoverageData(Collection $companies): array
    {
        $categories = ['Cover Limits', 'Third Party Limits', 'Excess', 'Additional Cover'];
        $result = [];

        foreach ($categories as $cat) {
            $itemNames = collect();
            foreach ($companies as $company) {
                $itemNames = $itemNames->merge(
                    $company->rates->where('category', $cat)->pluck('item_name')
                );
            }
            $itemNames = $itemNames->unique()->values();

            $rows = [];
            foreach ($itemNames as $itemName) {
                $row = ['item' => $itemName];
                foreach ($companies as $company) {
                    $rate = $company->rates
                        ->where('category', $cat)
                        ->where('item_name', $itemName)
                        ->first();
                    $row[$company->slug] = $rate ? $rate->display_value : "\u2014";
                }
                $rows[] = $row;
            }

            $result[$cat] = $rows;
        }

        return $result;
    }

    public function statsJson()
    {
        $companies = Company::active()->get();
        $companyStats = Quotation::selectRaw('company_id, COUNT(*) as total, SUM(total_premium) as total_premium')
            ->whereIn('company_id', $companies->pluck('id'))
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        $data = $companies->map(function ($c) use ($companyStats) {
            $stats = $companyStats->get($c->id);
            return [
                'slug' => $c->slug,
                'quote_count' => $stats ? (int) $stats->total : 0,
                'total_premium' => $stats ? (float) $stats->total_premium : 0,
            ];
        });

        return response()->json([
            'companies' => $data,
            'total_quotes' => $data->sum('quote_count'),
        ]);
    }

    public function psvInsurance(string $company)
    {
        $companies = Company::active()->with('rates')->get();
        $selectedCompany = $companies->firstWhere('slug', $company);
        if (!$selectedCompany) {
            abort(404, 'Insurance company not found.');
        }

        $vehicleType = 'psv';
        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B', '#7c3aed', '#0f7a65'];
        $numPassengers = request()->query('num_passengers', 1);

        $buildData = function($companies, $vehicleType) use ($companyColors) {
            return $companies->filter(function($c) use ($vehicleType) {
                return in_array($c->company_type, ['both', $vehicleType]);
            })->values()->map(function ($c, $i) use ($companyColors, $vehicleType) {
                $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
                $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
                $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
                return [
                    'slug' => $c->slug,
                    'name' => $c->name,
                    'logo' => $c->logoUrl(),
                    'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                    'taxRate' => 0,
                    'minPremium' => $rate ? (float) $rate->min_premium : 0,
                    'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                    'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                    'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                    'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                    'color' => $companyColors[$i % count($companyColors)],
                    'prefix' => $c->getInitials(),
                ];
            });
        };

        $companyData = $buildData($companies, 'psv');
        $privateCompanyData = $buildData($companies, 'private');
        $psvCompanyData = $buildData($companies, 'psv');

        $minSumInsured = (float)($selectedCompany->min_sum_insured_psv ?? 800000);
        $maxSumInsured = 10000000;

        $coverage = $this->getCoverageData($companies);

        return view('public.psv-insurance', compact(
            'companies', 'selectedCompany', 'companyData', 'privateCompanyData', 'psvCompanyData',
            'coverage', 'vehicleType', 'minSumInsured', 'maxSumInsured', 'numPassengers'
        ));
    }

    public function privateInsurance(string $company)
    {
        $companies = Company::active()->with('rates')->get();
        $selectedCompany = $companies->firstWhere('slug', $company);
        if (!$selectedCompany) {
            abort(404, 'Insurance company not found.');
        }

        $vehicleType = 'private';
        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B', '#7c3aed', '#0f7a65'];

        $buildData = function($companies, $vehicleType) use ($companyColors) {
            return $companies->filter(function($c) use ($vehicleType) {
                return in_array($c->company_type, ['both', $vehicleType]);
            })->values()->map(function ($c, $i) use ($companyColors, $vehicleType) {
                $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
                $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
                $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
                return [
                    'slug' => $c->slug,
                    'name' => $c->name,
                    'logo' => $c->logoUrl(),
                    'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                    'taxRate' => 0,
                    'minPremium' => $rate ? (float) $rate->min_premium : 0,
                    'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                    'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                    'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                    'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                    'color' => $companyColors[$i % count($companyColors)],
                    'prefix' => $c->getInitials(),
                ];
            });
        };

        $companyData = $buildData($companies, 'private');
        $privateCompanyData = $buildData($companies, 'private');
        $psvCompanyData = $buildData($companies, 'psv');

        $minSumInsured = (float)($selectedCompany->min_sum_insured_private ?? 500000);
        $maxSumInsured = 5000000;

        $coverage = $this->getCoverageData($companies);

        return view('public.private-insurance', compact(
            'companies', 'selectedCompany', 'companyData', 'privateCompanyData', 'psvCompanyData',
            'coverage', 'vehicleType', 'minSumInsured', 'maxSumInsured'
        ));
    }
}
