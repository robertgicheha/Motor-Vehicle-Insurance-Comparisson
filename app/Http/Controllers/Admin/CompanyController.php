<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Rate;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('quotations')->with('rates')->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:companies,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $company = Company::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditService::log('create', 'Company', $company->id, null, $company->toArray());
        AuditService::activity('company_created', "Company {$company->name} created");

        return redirect()->route('admin.companies.rates', $company)->with('success', 'Company created. Now add rates.');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:companies,name,' . $company->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $company->toArray();
        $company->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditService::log('update', 'Company', $company->id, $oldData, $company->toArray());
        AuditService::activity('company_updated', "Company {$company->name} updated");

        return redirect()->route('admin.companies.index')->with('success', 'Company updated.');
    }

    public function rates(Company $company)
    {
        $rates = $company->rates()->orderBy('category')->orderBy('item_name')->get()->groupBy('category');
        return view('admin.companies.rates', compact('company', 'rates'));
    }

    public function updateRate(Request $request, Company $company, Rate $rate)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'nullable|numeric',
            'percentage' => 'nullable|numeric|max:100',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'min_premium' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $rate->update($request->only(['value', 'percentage', 'min_value', 'max_value', 'min_premium']));

        AuditService::activity('rate_updated', "Rate {$rate->item_name} updated for {$company->name}");

        return back()->with('success', "{$rate->item_name} rate updated.");
    }

    public function addRate(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'item_name' => 'required|string',
            'value' => 'nullable|numeric',
            'value_type' => 'required|string',
            'percentage' => 'nullable|numeric|max:100',
            'min_value' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $company->rates()->create($request->all());
        AuditService::activity('rate_created', "Rate {$request->item_name} added for {$company->name}");

        return back()->with('success', 'Rate added.');
    }

    public function destroyRate(Company $company, Rate $rate)
    {
        $rate->delete();
        AuditService::activity('rate_deleted', "Rate {$rate->item_name} deleted for {$company->name}");
        return back()->with('success', 'Rate deleted.');
    }

    public function toggleStatus(Company $company)
    {
        $company->update(['is_active' => !$company->is_active]);
        AuditService::activity('company_toggled', "Company {$company->name} " . ($company->is_active ? 'activated' : 'deactivated'));
        return back()->with('success', 'Company status updated.');
    }
}
