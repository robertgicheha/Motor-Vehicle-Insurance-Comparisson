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
        return view('home', compact('companies', 'coverage', 'companyStats'));
    }

    public function quotation(?string $company = null)
    {
        $companies = Company::active()->with('rates')->get();
        $selectedCompany = null;

        if ($company) {
            $selectedCompany = $companies->firstWhere('slug', $company);
        }

        $coverage = $this->getCoverageData($companies);

        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
        $companyData = $companies->map(function ($c, $i) use ($companyColors) {
            $rate = $c->rates->firstWhere('item_name', 'Premium Rate');
            $iraLevy = $c->rates->firstWhere('item_name', 'IRA Levy');
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'taxRate' => $iraLevy ? (float) $iraLevy->percentage / 100 : 0.005,
                'color' => $companyColors[$i % count($companyColors)],
                'prefix' => $c->getInitials(),
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

        return view('quotation', compact('companies', 'selectedCompany', 'taxRate', 'coverage', 'companyData', 'userQuotation', 'hardcodedLogos', '__compareAllLogoUrl'));
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
        ]);

        $company = Company::where('slug', $request->company_slug)->firstOrFail();
        $premiumRate = $company->rates->firstWhere('item_name', 'Premium Rate');
        $iraLevyRate = $company->rates->firstWhere('item_name', 'IRA Levy');
        $ratePercent = $premiumRate ? (float) $premiumRate->percentage / 100 : 0;
        $iraLevyPercent = $iraLevyRate ? (float) $iraLevyRate->percentage / 100 : 0;
        $basicPremium = $request->car_valuation * $ratePercent;
        $iraLevy = $request->car_valuation * $iraLevyPercent;
        $basePremium = $basicPremium - $iraLevy;
        $total = $basicPremium;

        $quotation = auth()->user()->quotations()->create([
            'company_id' => $company->id,
            'quotation_number' => Quotation::generateNumber($company),
            'car_make' => $request->car_make,
            'car_model' => $request->car_model,
            'car_year' => $request->car_year,
            'car_registration' => $request->car_registration,
            'car_valuation' => $request->car_valuation,
            'premium' => $basicPremium,
            'pvt_premium' => 0,
            'total_premium' => $total,
            'excess_own_damage' => 0,
            'excess_theft' => 0,
            'status' => 'generated',
            'breakdown' => [
                'basic_premium' => $basicPremium,
                'base_premium' => $basePremium,
                'ira_levy' => $iraLevy,
                'premium_rate' => $ratePercent * 100,
                'ira_levy_rate' => $iraLevyPercent * 100,
            ],
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

        $companyColors = ['#003087', '#0066B3', '#00205B', '#D0021B'];
        $companyData = $companies->map(function ($c, $i) use ($companyColors) {
            $rate = $c->rates->firstWhere('item_name', 'Premium Rate');
            $iraLevy = $c->rates->firstWhere('item_name', 'IRA Levy');
            return [
                'slug' => $c->slug,
                'name' => $c->name,
                'logo' => $c->logoUrl(),
                'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                'taxRate' => $iraLevy ? (float) $iraLevy->percentage / 100 : 0.005,
                'color' => $companyColors[$i % count($companyColors)],
                'prefix' => $c->getInitials(),
            ];
        })->values();

        $coverage = $this->getCoverageData($companies);

        return view('public.generate-quote', compact('companies', 'selectedCompany', 'mode', 'companyData', 'coverage'));
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
}
