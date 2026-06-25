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
        return view('admin.rate-settings.index', compact('companies', 'totalCompanies', 'activeCompanies', 'totalRates'));
    }
}
