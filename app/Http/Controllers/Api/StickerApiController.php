<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Sticker;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StickerApiController extends Controller
{
    public function generate(Request $request)
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey || $apiKey !== Setting::getValue('sticker_api_key')) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'vehicle_registration' => 'nullable|string|max:50',
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:1990|max:2030',
            'car_valuation' => 'required|numeric|min:100000',
            'premium' => 'required|numeric',
            'premium_rate' => 'nullable|numeric|max:100',
            'cover_type' => 'nullable|string|max:100',
            'breakdown' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $company = Company::findOrFail($request->company_id);

        $sticker = Sticker::create([
            'company_id' => $request->company_id,
            'sticker_number' => Sticker::generateNumber(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'vehicle_registration' => $request->vehicle_registration,
            'car_make' => $request->car_make,
            'car_model' => $request->car_model,
            'car_year' => $request->car_year,
            'car_valuation' => $request->car_valuation,
            'premium' => $request->premium,
            'premium_rate' => $request->premium_rate,
            'cover_type' => $request->cover_type ?? 'Comprehensive',
            'status' => 'active',
            'created_via' => 'api',
            'breakdown' => $request->breakdown ?? [],
            'generated_at' => now(),
        ]);

        AuditService::log('create', 'Sticker', $sticker->id, null, $sticker->toArray());
        AuditService::activity('sticker_generated_api', "Sticker {$sticker->sticker_number} generated via API for {$company->name}");

        $sticker->load('company');

        $pdf = Pdf::loadView('pdf.sticker', ['sticker' => $sticker]);
        $pdfBase64 = base64_encode($pdf->output());

        return response()->json([
            'success' => true,
            'data' => [
                'sticker' => $sticker->toArray(),
                'company' => $company->name,
                'pdf_base64' => $pdfBase64,
                'pdf_filename' => "certificate-{$sticker->sticker_number}.pdf",
            ],
        ]);
    }
}
