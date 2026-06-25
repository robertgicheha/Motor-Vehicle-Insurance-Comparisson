<?php

namespace App\Http\Controllers;

use App\Mail\StickerMail;
use App\Models\Company;
use App\Models\Quotation;
use App\Models\Sticker;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StickerController extends Controller
{
    public function create(Request $request)
    {
        $quotation = null;
        if ($request->quotation_id) {
            $quotation = Quotation::with('company')->find($request->quotation_id);
        }
        $companies = Company::active()->get();
        return view('stickers.generate', compact('companies', 'quotation'));
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

    public function userLookup()
    {
        return view('stickers.user-lookup');
    }

    public function lookupByQuotation(Request $request)
    {
        $request->validate(['quotation_number' => 'required|string']);

        $quotation = Quotation::with('company')
            ->where('quotation_number', $request->quotation_number)
            ->first();

        if (!$quotation) {
            return response()->json(['error' => 'No quotation found with that number.'], 404);
        }

        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this quotation.'], 403);
        }

        $sticker = Sticker::where('quotation_id', $quotation->id)->first();

        return response()->json([
            'quotation' => [
                'id' => $quotation->id,
                'number' => $quotation->quotation_number,
                'company' => $quotation->company->name,
                'car_make' => $quotation->car_make,
                'car_model' => $quotation->car_model,
                'car_year' => $quotation->car_year,
                'car_registration' => $quotation->car_registration,
                'car_valuation' => $quotation->car_valuation,
                'total_premium' => $quotation->total_premium,
                'status' => $quotation->status,
                'created_at' => $quotation->created_at->format('d M Y'),
            ],
            'sticker' => $sticker ? [
                'id' => $sticker->id,
                'number' => $sticker->sticker_number,
                'download_url' => route('stickers.download', $sticker),
                'print_url' => route('stickers.print', $sticker),
                'show_url' => route('stickers.show', $sticker),
            ] : null,
            'can_generate' => !$sticker,
        ]);
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
