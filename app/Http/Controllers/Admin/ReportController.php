<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Sticker;
use App\Models\Company;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    private function quotationQuery(Request $request)
    {
        $query = Quotation::with(['user', 'company']);

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
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

    private function stickerQuery(Request $request)
    {
        $query = Sticker::with('company');

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
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
    public function index()
    {
        $companies = Company::active()->get();
        return view('admin.reports.index', compact('companies'));
    }

    public function stickerReport(Request $request)
    {
        $query = Sticker::with('company');

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
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

        $stickers = $query->latest()->get();

        $summary = [
            'total_stickers' => $stickers->count(),
            'total_premium' => $stickers->sum('premium'),
            'average_premium' => $stickers->avg('premium'),
            'total_downloads' => $stickers->sum('download_count'),
        ];

        $byCompany = $stickers->groupBy('company.name')->map(function ($items, $key) {
            return [
                'count' => $items->count(),
                'total_premium' => $items->sum('premium'),
                'average_premium' => $items->avg('premium'),
                'total_downloads' => $items->sum('download_count'),
            ];
        });

        $byDay = $stickers->groupBy(function ($s) {
            return $s->created_at->format('Y-m-d');
        })->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('premium'),
            ];
        });

        $byCreatedVia = $stickers->groupBy('created_via')->map(function ($items, $key) {
            return ['count' => $items->count()];
        });

        return response()->json([
            'stickers' => $stickers,
            'summary' => $summary,
            'by_company' => $byCompany,
            'by_day' => $byDay,
            'by_created_via' => $byCreatedVia,
        ]);
    }

    public function generate(Request $request)
    {
        $query = Quotation::with(['user', 'company']);

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
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

        $quotations = $query->latest()->get();

        $summary = [
            'total_quotations' => $quotations->count(),
            'total_premium' => $quotations->sum('total_premium'),
            'average_premium' => $quotations->avg('total_premium'),
            'average_valuation' => $quotations->avg('car_valuation'),
        ];

        $byCompany = $quotations->groupBy('company.name')->map(function ($items, $key) {
            return [
                'count' => $items->count(),
                'total_premium' => $items->sum('total_premium'),
                'average_premium' => $items->avg('total_premium'),
            ];
        });

        $byDay = $quotations->groupBy(function ($q) {
            return $q->created_at->format('Y-m-d');
        })->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('total_premium'),
            ];
        });

        return response()->json([
            'quotations' => $quotations,
            'summary' => $summary,
            'by_company' => $byCompany,
            'by_day' => $byDay,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = Quotation::with(['user', 'company']);

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $quotations = $query->get();
        $filename = 'quotations_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($quotations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Quotation #', 'Username', 'Company', 'Registration', 'Car Make', 'Car Model',
                'Car Year', 'Valuation (KES)', 'Premium (KES)', 'PVT (KES)',
                'Total Premium (KES)', 'Excess OD (KES)', 'Excess Theft (KES)',
                'Status', 'Date'
            ]);

            foreach ($quotations as $q) {
                fputcsv($handle, [
                    $q->quotation_number,
                    $q->user?->username,
                    $q->company->name,
                    $q->car_registration ?? '',
                    $q->car_make,
                    $q->car_model,
                    $q->car_year,
                    number_format($q->car_valuation, 2),
                    number_format($q->premium, 2),
                    number_format($q->pvt_premium, 2),
                    number_format($q->total_premium, 2),
                    number_format($q->excess_own_damage ?? 0, 2),
                    number_format($q->excess_theft ?? 0, 2),
                    $q->status,
                    $q->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request)
    {
        $query = Quotation::with(['user', 'company']);

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $quotations = $query->get();

        $spreadsheet = new \OpenSpout\Writer\XLSX\Writer();
        $filename = 'quotations_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/' . $filename);

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($filePath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Quotations');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Quotation #', 'Username', 'Company', 'Registration', 'Car Make', 'Car Model',
            'Car Year', 'Valuation (KES)', 'Premium (KES)', 'PVT (KES)',
            'Total Premium (KES)', 'Excess OD (KES)', 'Excess Theft (KES)',
            'Status', 'Date'
        ]));

        foreach ($quotations as $q) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $q->quotation_number,
                $q->user?->username,
                $q->company->name,
                $q->car_registration ?? '',
                $q->car_make,
                $q->car_model,
                $q->car_year,
                $q->car_valuation,
                $q->premium,
                $q->pvt_premium,
                $q->total_premium,
                $q->excess_own_damage ?? 0,
                $q->excess_theft ?? 0,
                $q->status,
                $q->created_at->format('Y-m-d H:i'),
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportStickersCsv(Request $request)
    {
        $query = Sticker::with('company');

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stickers = $query->get();
        $filename = 'certificates_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($stickers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Certificate #', 'Company',
                'Car Make', 'Car Model', 'Car Year', 'Registration',
                'Valuation (KES)', 'Premium (KES)', 'Rate (%)', 'Cover Type',
                'Status', 'Downloads', 'Created Via', 'Date'
            ]);

            foreach ($stickers as $s) {
                fputcsv($handle, [
                    $s->sticker_number,
                    $s->company->name,
                    $s->car_make,
                    $s->car_model,
                    $s->car_year,
                    $s->vehicle_registration ?? '',
                    number_format($s->car_valuation, 2),
                    number_format($s->premium, 2),
                    $s->premium_rate,
                    $s->cover_type,
                    $s->status,
                    $s->download_count,
                    strtoupper($s->created_via),
                    $s->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $quotations = $this->quotationQuery($request)->latest()->get();
        $company = $request->company_id ? Company::find($request->company_id) : null;

        $summary = [
            'total_quotations' => $quotations->count(),
            'total_premium' => $quotations->sum('total_premium'),
            'average_premium' => $quotations->avg('total_premium'),
            'average_valuation' => $quotations->avg('car_valuation'),
        ];

        $byCompany = $quotations->groupBy('company.name')->map(function ($items, $key) {
            return [
                'count' => $items->count(),
                'total_premium' => $items->sum('total_premium'),
                'average_premium' => $items->avg('total_premium'),
            ];
        });

        $byDay = $quotations->groupBy(function ($q) {
            return $q->created_at->format('Y-m-d');
        })->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('total_premium'),
            ];
        });

        $pdf = Pdf::loadView('pdf.report', compact('quotations', 'summary', 'byCompany', 'byDay', 'company') + ['request' => $request])
            ->setPaper('a4', 'landscape');

        return $pdf->download('quotations-report-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportStickersPdf(Request $request)
    {
        $stickers = $this->stickerQuery($request)->latest()->get();
        $company = $request->company_id ? Company::find($request->company_id) : null;

        $summary = [
            'total_stickers' => $stickers->count(),
            'total_premium' => $stickers->sum('premium'),
            'average_premium' => $stickers->avg('premium'),
            'total_downloads' => $stickers->sum('download_count'),
        ];

        $byCompany = $stickers->groupBy('company.name')->map(function ($items, $key) {
            return [
                'count' => $items->count(),
                'total_premium' => $items->sum('premium'),
                'average_premium' => $items->avg('premium'),
                'total_downloads' => $items->sum('download_count'),
            ];
        });

        $byDay = $stickers->groupBy(function ($s) {
            return $s->created_at->format('Y-m-d');
        })->map(function ($items) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('premium'),
            ];
        });

        $pdf = Pdf::loadView('pdf.stickers-report', compact('stickers', 'summary', 'byCompany', 'byDay', 'company') + ['request' => $request])
            ->setPaper('a4', 'landscape');

        return $pdf->download('certificates-report-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportStickersExcel(Request $request)
    {
        $stickers = $this->stickerQuery($request)->get();

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $filename = 'certificates_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/' . $filename);
        $writer->openToFile($filePath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Certificates');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Certificate #', 'Company',
            'Car Make', 'Car Model', 'Car Year', 'Registration',
            'Valuation (KES)', 'Premium (KES)', 'Rate (%)', 'Cover Type',
            'Status', 'Downloads', 'Created Via', 'Date'
        ]));

        foreach ($stickers as $s) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $s->sticker_number,
                $s->company->name,
                $s->car_make,
                $s->car_model,
                $s->car_year,
                $s->vehicle_registration ?? '',
                $s->car_valuation,
                $s->premium,
                $s->premium_rate,
                $s->cover_type,
                $s->status,
                $s->download_count,
                strtoupper($s->created_via),
                $s->created_at->format('Y-m-d H:i'),
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
