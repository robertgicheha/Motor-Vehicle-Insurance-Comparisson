<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Rate;

class RateSettingController extends Controller
{
    public function index()
    {
        $companies = Company::with('rates')->get();
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $totalRates = Rate::count();
        $privateRates = Rate::where('vehicle_type', 'private')->count();
        $psvRates = Rate::where('vehicle_type', 'psv')->count();

        $companyRateSummary = $companies->mapWithKeys(function ($c) {
            $private = $c->rates->where('vehicle_type', 'private');
            $psv = $c->rates->where('vehicle_type', 'psv');
            return [$c->id => [
                'private' => [
                    'count' => $private->count(),
                    'categories' => $private->groupBy('category')->map(fn($r) => $r->count()),
                ],
                'psv' => [
                    'count' => $psv->count(),
                    'categories' => $psv->groupBy('category')->map(fn($r) => $r->count()),
                ],
            ]];
        });

        return view('admin.rate-settings.index', compact('companies', 'totalCompanies', 'activeCompanies', 'totalRates', 'privateRates', 'psvRates', 'companyRateSummary'));
    }
}
