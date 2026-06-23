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
        return view('home', compact('companies', 'coverage'));
    }

    public function quotation(?string $company = null)
    {
        $companies = Company::active()->with('rates')->get();
        $selectedCompany = null;

        if ($company) {
            $selectedCompany = $companies->firstWhere('slug', $company);
        }

        $taxRate = 0.005;
        $coverage = $this->getCoverageData($companies);

        return view('quotation', compact('companies', 'selectedCompany', 'taxRate', 'coverage'));
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
}
