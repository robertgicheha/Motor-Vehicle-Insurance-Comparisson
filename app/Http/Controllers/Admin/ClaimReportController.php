<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClaimReportMail;
use App\Models\Claim;
use App\Models\Company;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class ClaimReportController extends Controller
{
    /**
     * Shared filter set for every report entry point below — company
     * (= "per underwriter"), user (= "per user", either the filer or the
     * customer the claim was logged on behalf of), status, claim type, and
     * a period/date-range pair. Mirrors ReportController's query-building
     * pattern for quotations/certificates.
     */
    private function claimQuery(Request $request)
    {
        $query = Claim::with(['user', 'onBehalfOfUser']);

        if ($request->filled('company_id')) {
            $query->whereRaw("JSON_EXTRACT(vehicle_details, '$.insurance_company_id') = ?", [$request->company_id]);
        }

        if ($request->filled('user_id')) {
            $userId = $request->user_id;
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('on_behalf_of_user_id', $userId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('claim_type')) {
            $query->where('claim_type', $request->claim_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($request->period === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->period === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        return $query;
    }

    private function companyNameFor(?int $companyId): string
    {
        if (!$companyId) {
            return 'N/A';
        }

        return Company::find($companyId)?->name ?? 'N/A';
    }

    private function buildResult(Request $request): array
    {
        $claims = $this->claimQuery($request)->latest()->get();

        $summary = [
            'total_claims' => $claims->count(),
            'total_settlement' => $claims->sum('settlement_amount'),
            'average_settlement' => $claims->where('settlement_amount', '>', 0)->avg('settlement_amount') ?? 0,
            'police_notified_count' => $claims->where('police_notified', true)->count(),
        ];

        $byCompany = $claims->groupBy(fn($c) => $this->companyNameFor($c->vehicle_details['insurance_company_id'] ?? null))
            ->map(fn($items) => ['count' => $items->count(), 'total_settlement' => $items->sum('settlement_amount')]);

        $byDay = $claims->groupBy(fn($c) => $c->created_at->format('Y-m-d'))
            ->map(fn($items) => ['count' => $items->count()]);

        $byStatus = $claims->groupBy('status')
            ->map(fn($items) => ['count' => $items->count()]);

        $byClaimType = $claims->groupBy(fn($c) => Claim::claimTypes()[$c->claim_type] ?? ($c->claim_type ?: 'Not specified'))
            ->map(fn($items) => ['count' => $items->count()]);

        $byUser = $claims->groupBy(fn($c) => $c->onBehalfOfUser?->name ?? $c->user?->name ?? 'Unknown')
            ->map(fn($items) => ['count' => $items->count()]);

        return compact('claims', 'summary', 'byCompany', 'byDay', 'byStatus', 'byClaimType', 'byUser');
    }

    public function index()
    {
        $companies = Company::active()->get();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $claimTypes = Claim::claimTypes();
        $statuses = Claim::statuses();

        return view('admin.claims.reports', compact('companies', 'users', 'claimTypes', 'statuses'));
    }

    public function generate(Request $request)
    {
        $result = $this->buildResult($request);

        return response()->json([
            'claims' => $result['claims'],
            'summary' => $result['summary'],
            'by_company' => $result['byCompany'],
            'by_day' => $result['byDay'],
            'by_status' => $result['byStatus'],
            'by_claim_type' => $result['byClaimType'],
            'by_user' => $result['byUser'],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $claims = $this->claimQuery($request)->latest()->get();
        $filename = 'claims_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($claims) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Claim #', 'Filed By', 'Customer', 'Registration', 'Underwriter',
                'Claim Type', 'Incident Date', 'Police Notified', 'Status',
                'Settlement (KES)', 'Date Logged',
            ]);

            foreach ($claims as $c) {
                fputcsv($handle, [
                    $c->claim_number,
                    $c->user?->name ?? 'N/A',
                    $c->onBehalfOfUser?->name ?? $c->customer_name,
                    $c->registration_number,
                    $this->companyNameFor($c->vehicle_details['insurance_company_id'] ?? null),
                    Claim::claimTypes()[$c->claim_type] ?? $c->claim_type,
                    optional($c->incident_date)->format('Y-m-d'),
                    $c->police_notified ? 'Yes' : 'No',
                    ucfirst($c->status),
                    number_format((float) $c->settlement_amount, 2),
                    $c->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request)
    {
        $claims = $this->claimQuery($request)->latest()->get();

        $filename = 'claims_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/' . $filename);

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($filePath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Claims');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Claim #', 'Filed By', 'Customer', 'Registration', 'Underwriter',
            'Claim Type', 'Incident Date', 'Police Notified', 'Status',
            'Settlement (KES)', 'Date Logged',
        ]));

        foreach ($claims as $c) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $c->claim_number,
                $c->user?->name ?? 'N/A',
                $c->onBehalfOfUser?->name ?? $c->customer_name,
                $c->registration_number,
                $this->companyNameFor($c->vehicle_details['insurance_company_id'] ?? null),
                Claim::claimTypes()[$c->claim_type] ?? $c->claim_type,
                optional($c->incident_date)->format('Y-m-d'),
                $c->police_notified ? 'Yes' : 'No',
                ucfirst($c->status),
                (float) $c->settlement_amount,
                $c->created_at->format('Y-m-d H:i'),
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function buildReportPdf(Request $request)
    {
        $result = $this->buildResult($request);
        $company = $request->filled('company_id') ? Company::find($request->company_id) : null;
        $filterUser = $request->filled('user_id') ? User::find($request->user_id) : null;

        return Pdf::loadView('pdf.claims-report', array_merge($result, [
            'company' => $company,
            'filterUser' => $filterUser,
            'request' => $request,
        ]))->setPaper('a4', 'landscape');
    }

    public function exportPdf(Request $request)
    {
        return $this->buildReportPdf($request)
            ->download('claims-report-' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Same document as exportPdf, but streamed inline instead of forced as a
     * download — opened in a new tab it renders in the browser's own PDF
     * viewer, which doubles as "print preview" for free.
     */
    public function previewPdf(Request $request)
    {
        return $this->buildReportPdf($request)
            ->stream('claims-report-preview.pdf');
    }

    public function emailReport(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $pdf = $this->buildReportPdf($request)->output();
        $company = $request->filled('company_id') ? Company::find($request->company_id) : null;
        $label = $company ? "Claims Report — {$company->name}" : 'Claims Report';

        try {
            Mail::to($request->email)->send(new ClaimReportMail($label, $pdf));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Report emailed to ' . $request->email . '.']);
    }

    /**
     * Compares up to 3 independently-filtered slices of claims data side by
     * side — e.g. this underwriter vs that one, or this month vs last. Each
     * entry in filters[] is the same shape of filter params the single
     * report accepts.
     */
    public function compare(Request $request)
    {
        $request->validate([
            'filters' => 'required|array|min:2|max:3',
            'filters.*.label' => 'nullable|string|max:100',
        ]);

        $results = [];
        foreach ($request->filters as $i => $filterSet) {
            $subRequest = Request::create('', 'GET', $filterSet);
            $result = $this->buildResult($subRequest);
            $results[] = [
                'label' => $filterSet['label'] ?? ('Set ' . ($i + 1)),
                'summary' => $result['summary'],
                'by_company' => $result['byCompany'],
                'by_status' => $result['byStatus'],
                'by_claim_type' => $result['byClaimType'],
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function comparePdf(Request $request)
    {
        $request->validate([
            'filters' => 'required|array|min:2|max:3',
        ]);

        $sections = [];
        foreach ($request->filters as $i => $filterSet) {
            $subRequest = Request::create('', 'GET', $filterSet);
            $result = $this->buildResult($subRequest);
            $company = !empty($filterSet['company_id']) ? Company::find($filterSet['company_id']) : null;
            $sections[] = array_merge($result, [
                'label' => $filterSet['label'] ?? ('Set ' . ($i + 1)),
                'company' => $company,
            ]);
        }

        $pdf = Pdf::loadView('pdf.claims-report-compare', ['sections' => $sections])
            ->setPaper('a4', 'landscape');

        return $pdf->download('claims-report-comparison-' . now()->format('Ymd_His') . '.pdf');
    }
}
