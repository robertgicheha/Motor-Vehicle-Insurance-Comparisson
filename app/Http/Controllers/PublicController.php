<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Quotation;
use App\Models\Rate;
use App\Models\Sticker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

        return redirect()->route('quotations.show', $quotation);
    }

    public function lookupSticker(Request $request)
    {
        $request->validate(['number' => 'required|string']);

        $sticker = Sticker::where('sticker_number', $request->number)->with('company')->first();

        if (!$sticker) {
            return back()->with('error', 'No certificate found with that number.');
        }

        return redirect()->route('stickers.show', $sticker);
    }

    public function globalSearch(Request $request)
    {
        if (!$request->q || strlen($request->q) < 2) {
            return response()->json(['quotations' => [], 'stickers' => []]);
        }

        $query = $request->q;

        $quotations = Quotation::where('quotation_number', 'like', "%{$query}%")
            ->orWhere('car_registration', 'like', "%{$query}%")
            ->orWhere('car_make', 'like', "%{$query}%")
            ->limit(5)->get(['id', 'quotation_number', 'car_make', 'car_model', 'car_registration']);

        $stickers = Sticker::where('sticker_number', 'like', "%{$query}%")
            ->orWhere('customer_name', 'like', "%{$query}%")
            ->orWhere('vehicle_registration', 'like', "%{$query}%")
            ->limit(5)->get(['id', 'sticker_number', 'customer_name', 'car_make', 'car_model']);

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
