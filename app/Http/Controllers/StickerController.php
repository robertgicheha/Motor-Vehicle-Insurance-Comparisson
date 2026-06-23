<?php

namespace App\Http\Controllers;

use App\Mail\StickerMail;
use App\Models\Company;
use App\Models\Sticker;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StickerController extends Controller
{
    public function create()
    {
        $companies = Company::active()->get();
        return view('stickers.generate', compact('companies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'vehicle_registration' => 'nullable|string|max:50',
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:1990|max:2030',
            'car_valuation' => 'required|numeric|min:100000',
            'premium' => 'required|numeric',
            'premium_rate' => 'nullable|numeric|max:100',
            'cover_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $company = Company::findOrFail($request->company_id);

        $sticker = Sticker::create([
            'company_id' => $request->company_id,
            'sticker_number' => Sticker::generateNumber(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'vehicle_registration' => $request->vehicle_registration,
            'car_make' => $request->car_make,
            'car_model' => $request->car_model,
            'car_year' => $request->car_year,
            'car_valuation' => $request->car_valuation,
            'premium' => $request->premium,
            'premium_rate' => $request->premium_rate,
            'cover_type' => $request->cover_type ?? 'Comprehensive',
            'status' => 'active',
            'created_via' => 'web',
            'breakdown' => $request->breakdown ?? [],
            'generated_at' => now(),
        ]);

        AuditService::log('create', 'Sticker', $sticker->id, null, $sticker->toArray());
        AuditService::activity('sticker_generated', "Sticker {$sticker->sticker_number} generated via web for {$company->name}");

        if ($request->customer_email) {
            try {
                Mail::to($request->customer_email)->send(new StickerMail($sticker));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send sticker email: " . $e->getMessage());
            }
        }

        return redirect()->route('stickers.show', $sticker)
            ->with('success', "Certificate {$sticker->sticker_number} generated successfully!");
    }

    public function show(Sticker $sticker)
    {
        $sticker->load(['company']);
        return view('stickers.show', compact('sticker'));
    }

    public function lookup()
    {
        return view('stickers.lookup');
    }

    public function search(Request $request)
    {
        $request->validate(['number' => 'required|string']);

        $sticker = Sticker::where('sticker_number', $request->number)->first();

        if (!$sticker) {
            return back()->with('error', 'No certificate found with that number.');
        }

        return redirect()->route('stickers.show', $sticker);
    }

    public function downloadPdf(Sticker $sticker)
    {
        $sticker->load('company');
        $sticker->increment('download_count');
        $sticker->update(['last_downloaded_at' => now()]);

        $pdf = Pdf::loadView('pdf.sticker', ['sticker' => $sticker]);
        return $pdf->download("certificate-{$sticker->sticker_number}.pdf");
    }

    public function printView(Sticker $sticker)
    {
        $sticker->load('company');
        $sticker->increment('download_count');
        $sticker->update(['last_downloaded_at' => now()]);

        return view('stickers.print', compact('sticker'));
    }
}
