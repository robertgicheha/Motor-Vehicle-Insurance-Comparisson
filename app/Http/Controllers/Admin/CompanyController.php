<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CompanyRatesImport;
use App\Imports\RatesImport;
use App\Models\ChangeRequest;
use App\Models\Company;
use App\Models\Rate;
use App\Notifications\NewChangeRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'company_type' => 'required|in:private,psv,both',
            'logo' => 'nullable|string|max:255',
            'claims_email' => 'nullable|email|max:255',
            'claims_name' => 'nullable|string|max:255',
            'claims_phone' => 'nullable|string|max:50',
            'cc_claims_email' => 'nullable|email|max:255',
            'underwriter_email' => 'nullable|email|max:255',
            'underwriter_name' => 'nullable|string|max:255',
            'underwriter_phone' => 'nullable|string|max:50',
            'sticker_notification_emails' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'company_type' => $request->company_type,
            'claims_email' => $request->claims_email,
            'claims_name' => $request->claims_name,
            'claims_phone' => $request->claims_phone,
            'cc_claims_email' => $request->cc_claims_email,
            'underwriter_email' => $request->underwriter_email,
            'underwriter_name' => $request->underwriter_name,
            'underwriter_phone' => $request->underwriter_phone,
            'sticker_notification_emails' => $request->sticker_notification_emails,
        ];

        if (auth()->user()->hasRole('admin')) {
            if ($request->filled('logo')) {
                $data['logo'] = $request->logo;
            }
            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'company',
                'action' => 'create',
                'data' => $data,
                'reason' => $request->input('reason', ''),
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('company_create_requested', "Company {$data['name']} creation requested by " . auth()->user()->name);
            return redirect()->route('admin.companies.index')->with('info', 'Your request to create a new company has been sent to the super-admin for approval.');
        }

        if ($request->filled('logo')) {
            $data['logo'] = $request->logo;
        }

        $company = Company::create($data);

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
            'company_type' => 'required|in:private,psv,both',
            'logo' => 'nullable|string|max:255',
            'claims_email' => 'nullable|email|max:255',
            'claims_name' => 'nullable|string|max:255',
            'claims_phone' => 'nullable|string|max:50',
            'cc_claims_email' => 'nullable|email|max:255',
            'underwriter_email' => 'nullable|email|max:255',
            'underwriter_name' => 'nullable|string|max:255',
            'underwriter_phone' => 'nullable|string|max:50',
            'sticker_notification_emails' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'company_type' => $request->company_type,
            'claims_email' => $request->claims_email,
            'claims_name' => $request->claims_name,
            'claims_phone' => $request->claims_phone,
            'cc_claims_email' => $request->cc_claims_email,
            'underwriter_email' => $request->underwriter_email,
            'underwriter_name' => $request->underwriter_name,
            'underwriter_phone' => $request->underwriter_phone,
            'sticker_notification_emails' => $request->sticker_notification_emails,
        ];

        if (auth()->user()->hasRole('admin')) {
            if ($request->filled('logo')) {
                $data['logo'] = $request->logo;
            }
            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'company',
                'action' => 'update',
                'data' => array_merge($data, ['company_id' => $company->id, 'company_name' => $company->name]),
                'reason' => $request->input('reason', ''),
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('company_update_requested', "Company {$company->name} update requested by " . auth()->user()->name);
            return redirect()->route('admin.companies.index')->with('info', 'Your update request has been sent to the super-admin for approval.');
        }

        if ($request->filled('logo')) {
            if ($company->logo && !str_starts_with($company->logo, 'images/')) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->logo;
        }

        $oldData = $company->toArray();
        $company->update($data);

        AuditService::log('update', 'Company', $company->id, $oldData, $company->toArray());
        AuditService::activity('company_updated', "Company {$company->name} updated");

        return redirect()->route('admin.companies.index')->with('success', 'Company updated.');
    }

    public function toggleStatus(Company $company)
    {
        if (auth()->user()->hasRole('admin')) {
            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'company',
                'action' => $company->is_active ? 'deactivate' : 'activate',
                'data' => ['company_id' => $company->id, 'company_name' => $company->name, 'is_active' => !$company->is_active],
                'reason' => '',
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('company_toggle_requested', "Company {$company->name} status change requested by " . auth()->user()->name);
            return back()->with('info', 'Your request to change the company status has been sent to the super-admin for approval.');
        }

        $company->update(['is_active' => !$company->is_active]);
        AuditService::log($company->is_active ? 'activate' : 'deactivate', 'Company', $company->id);
        AuditService::activity('company_toggled', "Company {$company->name} " . ($company->is_active ? 'activated' : 'deactivated'));
        return back()->with('success', 'Company status updated.');
    }

    // --- Rate Management ---

    public function rates(Company $company)
    {
        $rates = $company->rates()->orderBy('category')->orderBy('item_name')->get();
        $privateRates = $rates->where('vehicle_type', 'private')->groupBy('category');
        $psvRates = $rates->where('vehicle_type', 'psv')->groupBy('category');
        return view('admin.companies.rates', compact('company', 'rates', 'privateRates', 'psvRates'));
    }

    public function updateRate(Request $request, Company $company, Rate $rate)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'nullable|numeric',
            'percentage' => 'nullable|numeric|max:100',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'min_premium' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
            'value_type' => 'nullable|string|in:fixed,percentage,percentage_min,text',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if (auth()->user()->hasRole('admin')) {
            $changeData = $request->only(['value', 'percentage', 'min_value', 'max_value', 'min_premium', 'notes', 'value_type']);
            $changeData['rate_id'] = $rate->id;
            $changeData['rate_name'] = $rate->item_name;
            $changeData['company_id'] = $company->id;
            $changeData['company_name'] = $company->name;

            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'rate',
                'action' => 'update',
                'data' => $changeData,
                'reason' => $request->input('reason', ''),
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('rate_update_requested', "Rate {$rate->item_name} update for {$company->name} requested by " . auth()->user()->name);
            return back()->with('info', 'Your rate update request has been sent to the super-admin for approval.');
        }

        $rate->update($request->only(['value', 'percentage', 'min_value', 'max_value', 'min_premium', 'notes', 'value_type']));
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

        if (auth()->user()->hasRole('admin')) {
            $changeData = $request->only(['category', 'item_name', 'value', 'value_type', 'percentage', 'min_value']);
            $changeData['company_id'] = $company->id;
            $changeData['company_name'] = $company->name;

            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'rate',
                'action' => 'create',
                'data' => $changeData,
                'reason' => $request->input('reason', ''),
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('rate_create_requested', "New rate for {$company->name} requested by " . auth()->user()->name);
            return back()->with('info', 'Your rate creation request has been sent to the super-admin for approval.');
        }

        $company->rates()->create($request->all());
        AuditService::activity('rate_created', "Rate {$request->item_name} added for {$company->name}");
        return back()->with('success', 'Rate added.');
    }

    public function destroyRate(Company $company, Rate $rate)
    {
        if (auth()->user()->hasRole('admin')) {
            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'rate',
                'action' => 'delete',
                'data' => ['rate_id' => $rate->id, 'rate_name' => $rate->item_name, 'company_id' => $company->id, 'company_name' => $company->name],
                'reason' => '',
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('rate_delete_requested', "Rate {$rate->item_name} deletion for {$company->name} requested by " . auth()->user()->name);
            return back()->with('info', 'Your rate deletion request has been sent to the super-admin for approval.');
        }

        $rate->delete();
        AuditService::activity('rate_deleted', "Rate {$rate->item_name} deleted for {$company->name}");
        return back()->with('success', 'Rate deleted.');
    }

    public function importRates(Request $request, Company $company)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,json|max:5120',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $import = new RatesImport;
        $result = $import->import($file->getPathname(), $company->id, $extension);

        $message = "Imported {$result['imported']} rates.";
        if (!empty($result['errors'])) {
            return back()->with('import_errors', $result['errors'])->with('success', $message);
        }

        AuditService::activity('rates_imported', "{$result['imported']} rates imported for {$company->name}");
        return back()->with('success', $message);
    }

    public function importBulkCompanies(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:10240',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $import = new CompanyRatesImport;
        $result = $import->import($file->getPathname(), $extension);

        $message = "Imported {$result['imported']} rates across companies.";
        if (!empty($result['companies_created'])) {
            $message .= ' New companies: ' . implode(', ', $result['companies_created']) . '.';
        }

        if (!empty($result['errors'])) {
            return back()->with('import_errors', $result['errors'])->with('success', $message);
        }

        AuditService::activity('bulk_companies_imported', "Bulk companies import: {$result['imported']} rates");
        return back()->with('success', $message);
    }
}
