<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompareAllController extends Controller
{
    public function index()
    {
        $logo = Setting::getValue('compare_all_logo');
        return view('admin.compare-all.index', compact('logo'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->filled('logo')) {
            $path = $request->logo;
            $oldLogo = Setting::getValue('compare_all_logo');
            if ($oldLogo && !str_starts_with($oldLogo, 'images/')) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::setValue('compare_all_logo', $path, 'appearance');
            AuditService::activity('compare_all_logo_updated', 'Compare All card logo updated');
            return back()->with('success', 'Compare All card logo updated.');
        }

        return back()->with('error', 'No file selected.');
    }

    public function destroy()
    {
        $oldLogo = Setting::getValue('compare_all_logo');
        if ($oldLogo && !str_starts_with($oldLogo, 'images/')) {
            Storage::disk('public')->delete($oldLogo);
        }
        Setting::setValue('compare_all_logo', '', 'appearance');
        AuditService::activity('compare_all_logo_removed', 'Compare All card logo removed');
        return back()->with('success', 'Compare All card logo removed.');
    }
}
