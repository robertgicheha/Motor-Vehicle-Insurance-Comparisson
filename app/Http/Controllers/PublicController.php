<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
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
            $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
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
                'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
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
            $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
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
                'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
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
        $vehicleType = request()->query('vehicle_type', 'private');
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
            $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
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
                'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
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

        // On the "Compare All" page (no single company selected), fall back to the
        // user's most recent quotation of any company so the Customer & Vehicle
        // summary isn't blank for a logged-in user with nothing in sessionStorage —
        // e.g. landing here fresh rather than via the quote wizard.
        $userQuotation = null;
        if (auth()->check()) {
            $quotationQuery = auth()->user()->quotations();
            if ($selectedCompany) {
                $quotationQuery->where('company_id', $selectedCompany->id);
            }
            $userQuotation = $quotationQuery->latest()->first();
        }

        // Vehicle valuation, needed to resolve a percentage-based rate item (e.g.
        // "10%, min KES 50,000") into its actual computed amount. Not stored
        // server-side on this pre-save page — it lives in the browser's
        // sessionStorage — so it only arrives here if the caller passed it as a
        // query param (see public/js/home.js). Falls back to the logged-in
        // user's most recent quotation, same as $userQuotation above, so a
        // bookmarked/refreshed visit isn't left showing bare percentages.
        $carValuation = (float) request()->query('car_valuation', 0);
        if ($carValuation <= 0 && $userQuotation) {
            $carValuation = (float) $userQuotation->car_valuation;
        }

        $iraLevyRate = $selectedCompany?->rates->firstWhere('item_name', 'IRA Levy');
        $taxRate = $iraLevyRate ? (float) $iraLevyRate->percentage / 100 : 0.005;

        // No company picked (or an unknown slug) — show the standalone all-insurers
        // comparison view instead of the single-quotation one.
        if (!$selectedCompany) {
            $benefitsComparison = $this->benefitsComparisonMatrix($companiesForType, $vehicleType, $carValuation);

            return view('quotation-compare', compact('companies', 'taxRate', 'coverage', 'companyData', 'userQuotation', 'hardcodedLogos', '__compareAllLogoUrl', 'vehicleType', 'numPassengers', 'benefitsComparison'));
        }

        $rateCategories = $this->rateCategoriesFor($selectedCompany, $vehicleType);

        return view('quotation', compact('companies', 'selectedCompany', 'taxRate', 'coverage', 'companyData', 'userQuotation', 'hardcodedLogos', '__compareAllLogoUrl', 'vehicleType', 'numPassengers', 'rateCategories', 'carValuation'));
    }

    /**
     * Groups one company's rate settings into the standard quotation document
     * sections (mirrors QuotationController::quotationRateCategories, which
     * operates on a saved Quotation's company instead).
     */
    private function rateCategoriesFor(Company $company, string $vehicleType): array
    {
        $rates = $company->rates()
            ->where('vehicle_type', $vehicleType)
            ->get()
            ->groupBy('category');

        // Excess Protector and Loss of Use are always shown with the fixed
        // wording below (see SPECIAL CONDITIONS) instead of whatever the rate
        // settings say for these two items, so drop the real rows here.
        $isExcessProtectorOrLossOfUse = fn($r) => stripos($r->item_name, 'excess protector') !== false || stripos($r->item_name, 'loss of use') !== false;

        $additionalInfo = $rates->get('Optional Benefits', collect())
            ->merge($rates->get('Premium Rate', collect())->whereIn('item_name', ['Max Age New Business', 'Minimum Sum Insured']))
            ->reject($isExcessProtectorOrLossOfUse);

        // The authoritative minimum sum insured lives on the Company record
        // (min_sum_insured_private/psv) — not the "Minimum Sum Insured" Rate
        // row, which is commonly left at 0 since the real figure is
        // configured per company, not per rate item. Overriding it here (in
        // memory only, never persisted) is the same source the PDF partial
        // already uses, so this stops showing a stray "0".
        $minSumInsured = $vehicleType === 'psv' ? (float) $company->min_sum_insured_psv : (float) $company->min_sum_insured_private;
        if ($minSumInsured > 0) {
            $minSumInsuredItem = $additionalInfo->firstWhere('item_name', 'Minimum Sum Insured');
            if ($minSumInsuredItem) {
                $minSumInsuredItem->value = $minSumInsured;
                $minSumInsuredItem->percentage = null;
            } else {
                $additionalInfo->push(new Rate(['item_name' => 'Minimum Sum Insured', 'value' => $minSumInsured]));
            }
        }

        $specialConditions = $rates->get('Additional Cover', collect())
            ->reject($isExcessProtectorOrLossOfUse);

        // Excess Protector and Loss of Use are not offered as standard cover
        // by any underwriter in this comparison, so always show these fixed
        // rows rather than each company's (often absent or inconsistent)
        // rate-settings text for them.
        $specialConditions->push(new Rate(['item_name' => 'Excess Protector (Own Damage)', 'notes' => 'No Cover']));
        $specialConditions->push(new Rate(['item_name' => 'Excess Protector (Theft)', 'notes' => 'No Cover']));
        $specialConditions->push(new Rate(['item_name' => 'Loss of Use', 'notes' => 'Not Applicable']));

        return [
            'BENEFITS / COVER LIMITS' => $rates->get('Cover Limits', collect())
                ->merge($rates->get('Extras', collect()))
                ->reject(fn($r) => $r->item_name === 'Geographical Area'),
            'THIRD PARTY LIMITS OF LIABILITY' => $rates->get('Third Party Limits', collect()),
            'POLICY EXCESSES' => $rates->get('Excess', collect()),
            'SPECIAL CONDITIONS' => $specialConditions,
            'ADDITIONAL INFO' => $additionalInfo->reject(fn($r) => $r->item_name === 'Minimum Sum Insured'),
        ];
    }

    /**
     * Pivots each underwriter's rate categories into a side-by-side matrix —
     * one table per category, one row per benefit, one column per insurer —
     * instead of listing each company's benefits one after another (which
     * gives the reader nothing to actually compare).
     */
    private function benefitsComparisonMatrix($companiesForType, string $vehicleType, float $valuation = 0): array
    {
        // Excess Protector (Own Damage/Theft) and Loss of Use now live in the
        // Detailed Compare Table (Premium Comparison) instead, so drop them
        // here to avoid showing them twice.
        $movedToDetailedTable = ['Excess Protector (Own Damage)', 'Excess Protector (Theft)', 'Loss of Use'];

        $perCompany = $companiesForType->values()->map(function ($c) use ($vehicleType, $movedToDetailedTable) {
            $categories = $this->rateCategoriesFor($c, $vehicleType);
            $categories['SPECIAL CONDITIONS'] = $categories['SPECIAL CONDITIONS']->reject(fn($r) => in_array($r->item_name, $movedToDetailedTable));
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'categories' => $categories,
            ];
        });

        if ($perCompany->isEmpty()) {
            return [];
        }

        $categoryLabels = array_keys($perCompany->first()['categories']);
        $companyColumns = $perCompany->map(fn($c) => ['slug' => $c['slug'], 'name' => $c['name'], 'logo' => $c['logo']])->values();

        $matrix = [];
        foreach ($categoryLabels as $label) {
            $itemNames = collect();
            foreach ($perCompany as $company) {
                foreach ($company['categories'][$label] as $item) {
                    if (!$itemNames->contains($item->item_name)) {
                        $itemNames->push($item->item_name);
                    }
                }
            }

            if ($itemNames->isEmpty()) {
                continue;
            }

            $rows = $itemNames->map(function ($itemName) use ($perCompany, $label, $valuation) {
                return [
                    'item' => $itemName,
                    'values' => $perCompany->mapWithKeys(function ($company) use ($itemName, $label, $valuation) {
                        $match = $company['categories'][$label]->firstWhere('item_name', $itemName);
                        return [$company['slug'] => $this->formatRateItemValue($match, $valuation, $itemName)];
                    }),
                ];
            })->values();

            $matrix[$label] = ['companies' => $companyColumns, 'rows' => $rows];
        }

        return $matrix;
    }

    private function formatRateItemValue(?Rate $item, float $valuation = 0, ?string $itemName = null): string
    {
        // A benefit that another underwriter has but this one doesn't isn't
        // "unknown" (a dash) — it means this underwriter simply doesn't offer
        // it, so say so explicitly instead of leaving a blank-looking dash.
        if (!$item) {
            return 'No Cover';
        }
        // Decimal-cast columns (value/percentage) come back as numeric strings
        // (e.g. "0.00"), which PHP treats as truthy — so a real 0 must be
        // compared numerically, not with a bare truthiness check, or a
        // company with no cover for an item shows a bogus "KES 0" instead of
        // falling through to its Notes wording (No Cover, Covered, etc.).
        $value = (float) ($item->value ?? 0);
        $percentage = (float) ($item->percentage ?? 0);

        if ($item->item_name === 'Max Age New Business' && $value > 0) {
            return number_format($value, 0) . ' years';
        }
        if ($percentage > 0) {
            if ($valuation > 0) {
                $formula = $item->notes ?: (Rate::fmtPercent($item->percentage) . '% Min KES ' . number_format((float) ($item->min_value ?? 0), 0));
                return 'KES ' . number_format((float) ($item->computeAmountFor($valuation) ?? 0), 2)
                    . ' <span class="text-[10px] text-gray-400 font-normal">(' . e($formula) . ')</span>';
            }
            return Rate::fmtPercent($item->percentage) . '%';
        }
        if ($value > 0) {
            $additional = (stripos($item->item_name, 'novice') !== false || stripos($item->item_name, 'young driver') !== false) ? ' additional' : '';
            return 'KES ' . number_format($value, 0) . $additional;
        }
        if ($item->notes) {
            return $item->notes;
        }
        if (stripos($item->item_name, 'tracking device') !== false) {
            return 'No Cover';
        }
        return '—';
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
            \App\Services\AuditService::activity('quotation_emailed', "Quotation {$quotation->quotation_number} emailed to {$request->email}");
            return back()->with('success', 'Quotation sent to ' . $request->email);
        } catch (\Exception $e) {
            \App\Services\AuditService::activity('quotation_email_failed', "Failed to email quotation {$quotation->quotation_number} to {$request->email}: {$e->getMessage()}", 'error');
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
            'color' => 'nullable|string|max:50',
            'car_valuation' => 'required|numeric|min:800000|max:1300000',
            'vehicle_type' => 'sometimes|string|in:psv,private',
            'num_passengers' => 'sometimes|integer|min:1',
            'salutation' => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'chassis_number' => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
            'body_type' => 'nullable|string|max:100',
        ]);

        $company = Company::where('slug', $request->company_slug)->firstOrFail();
        $vehicleType = $request->input('vehicle_type', 'private');
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

        $user = auth()->user();
        $quotation = $user->quotations()->create([
            'salutation' => $request->salutation,
            'first_name' => $request->filled('first_name') ? $request->first_name : $user->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->filled('last_name') ? $request->last_name : $user->last_name,
            'phone' => $request->filled('phone') ? $request->phone : $user->mobile,
            'email' => $request->filled('email') ? $request->email : $user->email,
            'company_id' => $company->id,
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
                'premium' => $result['basic_premium'],
                'total_premium' => $result['total_premium'],
            ],
        ]);
    }

    public function globalSearch(Request $request)
    {
        $q = $request->q;
        if (!$q || strlen($q) < 2) {
            return response()->json(['quotations' => [], 'stickers' => []]);
        }

        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isAdmin = $user->hasRole('admin');

        // Same visibility rule as the quotations list: super-admin sees
        // everything; admin sees their own plus every 'user'-role account's
        // records; a plain user sees only their own. The search-term
        // matching is grouped into its own where(function...) so the
        // visibility scope ANDs against the whole OR-group, not just the
        // last orWhere term (SQL operator precedence: AND binds tighter
        // than OR, so appending a bare ->where() after ->orWhere() chains
        // would otherwise only scope the final term).
        $quotations = Quotation::where(function ($query) use ($q) {
                $query->where('quotation_number', 'like', "%{$q}%")
                    ->orWhereLikeCI('car_registration', $q)
                    ->orWhere('car_make', 'like', "%{$q}%")
                    ->orWhere('car_model', 'like', "%{$q}%");
            })
            ->when(!$isSuperAdmin, function ($query) use ($user, $isAdmin) {
                if ($isAdmin) {
                    return $query->where(function ($sub) use ($user) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('user', fn($uq) => $uq->role('user'));
                    });
                }
                return $query->where('user_id', $user->id);
            })
            ->limit(5)
            ->get(['id', 'quotation_number', 'car_make', 'car_model', 'car_registration']);

        $stickers = Sticker::where(function ($query) use ($q) {
                $query->where('sticker_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhereLikeCI('vehicle_registration', $q)
                    ->orWhere('car_make', 'like', "%{$q}%")
                    ->orWhere('car_model', 'like', "%{$q}%");
            })
            ->when(!$isSuperAdmin, function ($query) use ($user, $isAdmin) {
                if ($isAdmin) {
                    return $query->where(function ($sub) use ($user) {
                        $sub->where('issued_by', $user->id)
                            ->orWhereHas('issuedBy', fn($uq) => $uq->role('user'));
                    });
                }
                return $query->where('issued_by', $user->id);
            })
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
            $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
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
                'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
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
            $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
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
                'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
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
            $rateAbove = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate Above 2.5M');
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
                'rateAbove2_5M' => $rateAbove ? (float) $rateAbove->percentage / 100 : 0,
                'minPremiumAbove2_5M' => $rateAbove ? (float) $rateAbove->min_premium : 0,
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
