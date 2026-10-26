<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Sticker;
use App\Models\Company;
use App\Models\Rate;
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
    /**
     * Build the full analytics payload (quantitative + qualitative + forecast)
     * shared by the JSON preview endpoints and the PDF exports.
     */
    private function buildAnalytics($records, string $premiumField, string $unit, bool $withDownloads = false): array
    {
        $premiums = $records->pluck($premiumField)->map(fn ($v) => (float) $v)->filter(fn ($v) => $v !== null)->values();
        $valuations = $records->pluck('car_valuation')->map(fn ($v) => (float) $v)->filter(fn ($v) => $v !== null)->values();
        $count = $records->count();
        $totalPremium = (float) $premiums->sum();

        $sorted = $premiums->sort()->values();
        $mid = intdiv($sorted->count(), 2);
        $median = 0.0;
        if ($sorted->count()) {
            $median = $sorted->count() % 2
                ? (float) $sorted[$mid]
                : ((float) $sorted[$mid - 1] + (float) $sorted[$mid]) / 2;
        }

        $summary = [
            'total_count' => $count,
            'total_premium' => round($totalPremium, 2),
            'average_premium' => $count ? round($totalPremium / $count, 2) : 0.0,
            'median_premium' => round($median, 2),
            'min_premium' => round((float) ($premiums->min() ?? 0), 2),
            'max_premium' => round((float) ($premiums->max() ?? 0), 2),
            'average_valuation' => $valuations->count() ? round($valuations->sum() / $valuations->count(), 2) : 0.0,
            'total_valuation' => round((float) $valuations->sum(), 2),
            'total_downloads' => $withDownloads ? (int) $records->sum('download_count') : 0,
        ];

        $byCompany = $records->groupBy(fn ($r) => $r->company?->name ?? 'Unassigned')
            ->map(fn ($items) => [
                'count' => $items->count(),
                'total_premium' => round((float) $items->sum($premiumField), 2),
                'average_premium' => $items->count() ? round((float) $items->sum($premiumField) / $items->count(), 2) : 0.0,
            ])
            ->map(function ($item, $name) use ($totalPremium) {
                $item['share'] = $totalPremium > 0 ? round(($item['total_premium'] / $totalPremium) * 100, 2) : 0.0;
                return $item;
            })
            ->sortByDesc('total_premium');

        $byStatus = $records->groupBy(fn ($r) => $r->status ?: 'unknown')
            ->map(fn ($items) => $items->count())
            ->sortDesc();

        $monthly = $this->monthlySeries($records, $premiumField);

        $byDay = $records->groupBy(fn ($r) => $r->created_at->format('Y-m-d'))
            ->map(fn ($items) => ['count' => $items->count(), 'total' => round((float) $items->sum($premiumField), 2)])
            ->sortKeys();

        $valuationBuckets = ['Under 500K' => 0, '500K - 1M' => 0, '1M - 2M' => 0, '2M - 5M' => 0, 'Over 5M' => 0];
        foreach ($valuations as $v) {
            if ($v < 500000) {
                $valuationBuckets['Under 500K']++;
            } elseif ($v < 1000000) {
                $valuationBuckets['500K - 1M']++;
            } elseif ($v < 2000000) {
                $valuationBuckets['1M - 2M']++;
            } elseif ($v < 5000000) {
                $valuationBuckets['2M - 5M']++;
            } else {
                $valuationBuckets['Over 5M']++;
            }
        }

        $forecast = $this->forecast($monthly, 3);

        $analytics = compact('summary', 'byCompany', 'byStatus', 'monthly', 'byDay', 'forecast');
        $analytics['valuationBuckets'] = $valuationBuckets;
        $analytics['insights'] = $this->buildInsights($analytics, $unit);

        return $analytics;
    }

    /**
     * Continuous 12-month series ending with the current month.
     */
    private function monthlySeries($records, string $premiumField): array
    {
        $start = now()->startOfMonth()->subMonths(11);
        $labels = $counts = $premiums = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $items = $records->filter(fn ($r) => $r->created_at->format('Y-m') === $month->format('Y-m'));
            $labels[] = $month->format('M Y');
            $counts[] = $items->count();
            $premiums[] = round((float) $items->sum($premiumField), 2);
        }

        return ['labels' => $labels, 'counts' => $counts, 'premiums' => $premiums];
    }

    /**
     * Least-squares linear projection for the next N periods.
     */
    private function forecast(array $monthly, int $periods = 3): array
    {
        $project = function (array $series) use ($periods) {
            $n = count($series);
            if ($n === 0) {
                return array_fill(0, $periods, 0.0);
            }
            if ($n === 1) {
                return array_fill(0, $periods, max(0.0, (float) $series[0]));
            }

            $sumX = $sumY = $sumXY = $sumXX = 0;
            foreach (array_values($series) as $x => $y) {
                $sumX += $x;
                $sumY += $y;
                $sumXY += $x * $y;
                $sumXX += $x * $x;
            }

            $denom = ($n * $sumXX) - ($sumX * $sumX);
            $slope = $denom != 0 ? (($n * $sumXY) - ($sumX * $sumY)) / $denom : 0;
            $intercept = ($sumY - $slope * $sumX) / $n;

            $out = [];
            for ($i = 0; $i < $periods; $i++) {
                $out[] = max(0.0, $slope * ($n + $i) + $intercept);
            }
            return $out;
        };

        $labels = [];
        $lastMonth = now()->startOfMonth();
        for ($i = 1; $i <= $periods; $i++) {
            $labels[] = $lastMonth->copy()->addMonths($i)->format('M Y');
        }

        return [
            'labels' => $labels,
            'counts' => array_map(fn ($v) => round($v, 1), $project($monthly['counts'])),
            'premiums' => array_map(fn ($v) => round($v, 2), $project($monthly['premiums'])),
        ];
    }

    /**
     * Narrative (qualitative) observations derived from the quantitative data.
     */
    private function buildInsights(array $a, string $unit): array
    {
        $insights = [];
        $s = $a['summary'];
        $counts = $a['monthly']['counts'];
        $n = count($counts);

        if ($n >= 2) {
            $last = $counts[$n - 1];
            $prev = $counts[$n - 2];
            if ($prev > 0) {
                $pct = (($last - $prev) / $prev) * 100;
                $insights[] = sprintf(
                    '%s volume %s %.1f%% month-on-month (%d vs %d in the prior month).',
                    $unit,
                    $pct >= 0 ? 'grew' : 'fell',
                    abs($pct),
                    $last,
                    $prev
                );
            } elseif ($last > 0) {
                $insights[] = sprintf('%s activity resumed this month with %d new record(s) after a zero prior month.', $unit, $last);
            }
        }

        if ($a['byCompany']->isNotEmpty()) {
            $topName = $a['byCompany']->keys()->first();
            $top = $a['byCompany']->first();
            $insights[] = sprintf(
                '%s leads the portfolio with %d record(s), %.1f%% of premium and KES %s in total premium.',
                $topName,
                $top['count'],
                $top['share'],
                number_format($top['total_premium'], 2)
            );
        }

        if ($s['total_count'] > 0) {
            $peakIndex = array_search(max($counts), $counts, true);
            $insights[] = sprintf(
                'Premium averages KES %s (median KES %s) against an average vehicle valuation of KES %s; the busiest period was %s.',
                number_format($s['average_premium'], 2),
                number_format($s['median_premium'], 2),
                number_format($s['average_valuation'], 0),
                $a['monthly']['labels'][$peakIndex] ?? 'n/a'
            );
        }

        $nextCount = $a['forecast']['counts'][0] ?? 0;
        $nextLabel = $a['forecast']['labels'][0] ?? 'the next period';
        $insights[] = sprintf(
            'Linear-trend projection for %s is approximately %s record(s) (KES %s in premium), indicating the expected near-term trajectory.',
            $nextLabel,
            number_format($nextCount, 0),
            number_format($a['forecast']['premiums'][0] ?? 0, 0)
        );

        if ($a['byStatus']->count()) {
            $insights[] = sprintf(
                'The dominant status is "%s" with %d of %d records.',
                ucfirst($a['byStatus']->keys()->first()),
                $a['byStatus']->first(),
                $s['total_count']
            );
        }

        return $insights;
    }

    public function index()
    {
        $companies = Company::active()->get();

        $stats = (object) [
            'quotations_count' => Quotation::count(),
            'stickers_count' => Sticker::count(),
            'total_premium' => (float) Quotation::sum('total_premium') + (float) Sticker::sum('premium'),
        ];

        return view('admin.reports.index', compact('companies', 'stats'));
    }

    public function stickerReport(Request $request)
    {
        $stickers = $this->stickerQuery($request)->latest()->get();

        return response()->json([
            'stickers' => $stickers,
            'analytics' => $this->buildAnalytics($stickers, 'premium', 'Stickers', true),
        ]);
    }

    public function generate(Request $request)
    {
        $quotations = $this->quotationQuery($request)->latest()->get();

        return response()->json([
            'quotations' => $quotations,
            'analytics' => $this->buildAnalytics($quotations, 'total_premium', 'Quotations'),
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
        $analytics = $this->buildAnalytics($quotations, 'total_premium', 'Quotations');

        $pdf = Pdf::loadView('pdf.report', [
            'quotations' => $quotations,
            'analytics' => $analytics,
            'company' => $company,
            'request' => $request,
            'unit' => 'Quotations',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('quotations-report-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportStickersPdf(Request $request)
    {
        $stickers = $this->stickerQuery($request)->latest()->get();
        $company = $request->company_id ? Company::find($request->company_id) : null;
        $analytics = $this->buildAnalytics($stickers, 'premium', 'Stickers', true);

        $pdf = Pdf::loadView('pdf.stickers-report', [
            'stickers' => $stickers,
            'analytics' => $analytics,
            'company' => $company,
            'request' => $request,
            'unit' => 'Stickers',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('certificates-report-' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Same document as exportPdf, but streamed inline so the browser's own
     * PDF viewer renders it — used by the report page's live preview pane.
     */
    public function previewPdf(Request $request)
    {
        $quotations = $this->quotationQuery($request)->latest()->get();
        $company = $request->company_id ? Company::find($request->company_id) : null;
        $analytics = $this->buildAnalytics($quotations, 'total_premium', 'Quotations');

        return Pdf::loadView('pdf.report', [
            'quotations' => $quotations,
            'analytics' => $analytics,
            'company' => $company,
            'request' => $request,
            'unit' => 'Quotations',
        ])->setPaper('a4', 'landscape')->stream('quotations-report-preview.pdf');
    }

    /**
     * Inline preview of the stickers/certificates report (see previewPdf).
     */
    public function previewStickersPdf(Request $request)
    {
        $stickers = $this->stickerQuery($request)->latest()->get();
        $company = $request->company_id ? Company::find($request->company_id) : null;
        $analytics = $this->buildAnalytics($stickers, 'premium', 'Stickers', true);

        return Pdf::loadView('pdf.stickers-report', [
            'stickers' => $stickers,
            'analytics' => $analytics,
            'company' => $company,
            'request' => $request,
            'unit' => 'Stickers',
        ])->setPaper('a4', 'landscape')->stream('certificates-report-preview.pdf');
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

    // ---------------------------------------------------------------------
    // Users (performance) and Underwriters (availability) reports
    // ---------------------------------------------------------------------

    /**
     * Apply the shared period/date-range scope to a query that has a
     * created_at column.
     */
    private function applyPeriodScope($query, Request $request)
    {
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

    /**
     * Continuous 12-month activity series (quotations + stickers combined)
     * for the user & underwriter reports. $applyCompany lets the underwriter
     * report ignore the company filter so its monthly series stays
     * consistent with the portfolio-wide entity list.
     */
    private function activityMonthly(Request $request, bool $applyCompany = true): array
    {
        $companyFilter = fn ($q) => $q->when($applyCompany && $request->company_id, fn ($sub) => $sub->where('company_id', $request->company_id));

        $quotations = $this->applyPeriodScope($companyFilter(Quotation::query()), $request)
            ->get(['created_at', 'total_premium']);
        $stickers = $this->applyPeriodScope($companyFilter(Sticker::query()), $request)
            ->get(['created_at', 'premium']);

        $activity = $quotations
            ->map(fn ($r) => ['date' => $r->created_at, 'amount' => (float) $r->total_premium])
            ->concat($stickers->map(fn ($r) => ['date' => $r->created_at, 'amount' => (float) $r->premium]));

        $start = now()->startOfMonth()->subMonths(11);
        $labels = $counts = $premiums = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $items = $activity->filter(fn ($a) => $a['date']->format('Y-m') === $month->format('Y-m'));
            $labels[] = $month->format('M Y');
            $counts[] = $items->count();
            $premiums[] = round((float) $items->sum('amount'), 2);
        }

        return ['labels' => $labels, 'counts' => $counts, 'premiums' => $premiums];
    }

    private function buildUserReport(Request $request): array
    {
        $users = User::query()->orderBy('username')->get();

        $quotes = $this->applyPeriodScope(
            Quotation::query()->when($request->company_id, fn ($q) => $q->where('company_id', $request->company_id)),
            $request
        )->selectRaw('user_id, COUNT(*) as c, COALESCE(SUM(total_premium), 0) as p')
            ->groupBy('user_id')->get()->keyBy('user_id');

        $stickers = $this->applyPeriodScope(
            Sticker::query()->when($request->company_id, fn ($q) => $q->where('company_id', $request->company_id)),
            $request
        )->selectRaw('issued_by, COUNT(*) as c, COALESCE(SUM(premium), 0) as p')
            ->groupBy('issued_by')->get()->keyBy('issued_by');

        $rows = $users->map(function (User $u) use ($quotes, $stickers) {
            $q = $quotes->get($u->id);
            $s = $stickers->get($u->id);
            $qCount = (int) ($q->c ?? 0);
            $sCount = (int) ($s->c ?? 0);

            return [
                'id' => $u->id,
                'username' => $u->username,
                'branch' => $u->branch,
                'is_active' => (bool) $u->is_active,
                'roles' => $u->getRoleNames()->implode(', '),
                'last_online_at' => $u->last_online_at?->format('Y-m-d H:i'),
                'quotations_count' => $qCount,
                'stickers_count' => $sCount,
                'total_premium' => round((float) ($q->p ?? 0) + (float) ($s->p ?? 0), 2),
                'conversion' => $qCount > 0 ? round(($sCount / $qCount) * 100, 1) : 0.0,
            ];
        })->values();

        $monthly = $this->activityMonthly($request);

        $active = $rows->where('is_active', true)->count();
        $inactive = $rows->where('is_active', false)->count();
        $totalQuotes = (int) $rows->sum('quotations_count');
        $totalStickers = (int) $rows->sum('stickers_count');
        $totalPremium = round((float) $rows->sum('total_premium'), 2);
        $withActivity = $rows->filter(fn ($r) => ($r['quotations_count'] + $r['stickers_count']) > 0)->count();

        $summary = [
            'total_users' => $rows->count(),
            'active_users' => $active,
            'inactive_users' => $inactive,
            'users_with_activity' => $withActivity,
            'total_quotations' => $totalQuotes,
            'total_stickers' => $totalStickers,
            'total_premium' => $totalPremium,
            'average_premium_per_user' => $withActivity ? round($totalPremium / $withActivity, 2) : 0.0,
            'conversion_rate' => $totalQuotes > 0 ? round(($totalStickers / $totalQuotes) * 100, 1) : 0.0,
        ];

        $byBranch = $rows->groupBy(fn ($r) => $r['branch'] ?: 'Unassigned')
            ->map(fn ($items) => [
                'count' => $items->count(),
                'total_premium' => round((float) $items->sum('total_premium'), 2),
                'average_premium' => $items->count() ? round((float) $items->sum('total_premium') / $items->count(), 2) : 0.0,
            ])
            ->map(function ($item) use ($totalPremium) {
                $item['share'] = $totalPremium > 0 ? round(($item['total_premium'] / $totalPremium) * 100, 2) : 0.0;
                return $item;
            })
            ->sortByDesc('total_premium');

        $byStatus = collect(['active' => $active, 'inactive' => $inactive])->filter(fn ($v) => $v > 0);

        $sorted = $rows->sortByDesc('total_premium')->values();

        $analytics = [
            'summary' => $summary,
            'byCompany' => $byBranch,
            'distribution' => $rows->groupBy(fn ($r) => $r['branch'] ?: 'Unassigned')->map->count()->sortDesc(),
            'byStatus' => $byStatus,
            'monthly' => $monthly,
            'forecast' => $this->forecast($monthly, 3),
            'insights' => [],
        ];

        $insights = [];
        if ($summary['total_users'] > 0) {
            $insights[] = sprintf(
                '%d of %d accounts (%.0f%%) are active; %d generated quotations or certificates in the selected scope.',
                $active,
                $summary['total_users'],
                ($active / $summary['total_users']) * 100,
                $withActivity
            );
        }
        $top = $sorted->first();
        if ($top && $top['total_premium'] > 0) {
            $insights[] = sprintf(
                '%s is the top performer with KES %s generated across %d quotation(s) and %d certificate(s).',
                $top['username'],
                number_format($top['total_premium'], 2),
                $top['quotations_count'],
                $top['stickers_count']
            );
        }
        if ($byBranch->isNotEmpty()) {
            $topBranch = $byBranch->keys()->first();
            $insights[] = sprintf('%s is the strongest branch, contributing %.1f%% of generated premium.', $topBranch, $byBranch->first()['share']);
        }
        $insights[] = sprintf('Quotation-to-certificate conversion across all users is %.1f%%.', $summary['conversion_rate']);
        $insights[] = sprintf(
            'Projected activity for %s is approximately %s record(s).',
            $analytics['forecast']['labels'][0] ?? 'the next period',
            number_format($analytics['forecast']['counts'][0] ?? 0, 0)
        );
        $analytics['insights'] = $insights;

        return ['users' => $sorted, 'analytics' => $analytics];
    }

    private function buildUnderwriterReport(Request $request): array
    {
        $companies = Company::query()->orderBy('name')->get();

        $ratesByCompany = Rate::query()
            ->get(['company_id', 'category', 'vehicle_type'])
            ->groupBy('company_id');

        $quotes = $this->applyPeriodScope(Quotation::query(), $request)
            ->selectRaw('company_id, COUNT(*) as c, COALESCE(SUM(total_premium), 0) as p')
            ->groupBy('company_id')->get()->keyBy('company_id');

        $stickers = $this->applyPeriodScope(Sticker::query(), $request)
            ->selectRaw('company_id, COUNT(*) as c, COALESCE(SUM(premium), 0) as p')
            ->groupBy('company_id')->get()->keyBy('company_id');

        $rows = $companies->map(function (Company $c) use ($ratesByCompany, $quotes, $stickers) {
            $companyRates = $ratesByCompany->get($c->id, collect());
            $categories = $companyRates
                ->flatMap(fn ($r) => array_filter([$r->category, $r->vehicle_type]))
                ->unique()->values();
            $q = $quotes->get($c->id);
            $s = $stickers->get($c->id);

            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'is_active' => (bool) $c->is_active,
                'rates_count' => $companyRates->count(),
                'categories' => $categories->implode(', '),
                'categories_count' => $categories->count(),
                'min_sum_insured_psv' => (float) $c->min_sum_insured_psv,
                'min_sum_insured_private' => (float) $c->min_sum_insured_private,
                'underwriter_email' => $c->underwriter_email,
                'claims_email' => $c->claims_email,
                'quotations_count' => (int) ($q->c ?? 0),
                'stickers_count' => (int) ($s->c ?? 0),
                'total_premium' => round((float) ($q->p ?? 0) + (float) ($s->p ?? 0), 2),
            ];
        })->values();

        $monthly = $this->activityMonthly($request, false);

        $active = $rows->where('is_active', true)->count();
        $inactive = $rows->where('is_active', false)->count();
        $configured = $rows->filter(fn ($r) => $r['rates_count'] > 0)->count();
        $totalPremium = round((float) $rows->sum('total_premium'), 2);

        $summary = [
            'total_underwriters' => $rows->count(),
            'active_underwriters' => $active,
            'inactive_underwriters' => $inactive,
            'configured_underwriters' => $configured,
            'total_rates' => (int) $rows->sum('rates_count'),
            'total_quotations' => (int) $rows->sum('quotations_count'),
            'total_stickers' => (int) $rows->sum('stickers_count'),
            'total_premium' => $totalPremium,
        ];

        $byCompany = $rows->sortByDesc('total_premium')->mapWithKeys(fn ($r) => [
            $r['name'] => [
                'count' => $r['quotations_count'] + $r['stickers_count'],
                'total_premium' => $r['total_premium'],
            ],
        ])->map(function ($item) use ($totalPremium) {
            $item['share'] = $totalPremium > 0 ? round(($item['total_premium'] / $totalPremium) * 100, 2) : 0.0;
            $item['average_premium'] = $item['count'] ? round($item['total_premium'] / $item['count'], 2) : 0.0;
            return $item;
        });

        $byStatus = collect(['active' => $active, 'inactive' => $inactive])->filter(fn ($v) => $v > 0);

        $sorted = $rows->sortBy('name')->values();

        $analytics = [
            'summary' => $summary,
            'byCompany' => $byCompany,
            'rates' => $rows->sortByDesc('rates_count')->mapWithKeys(fn ($r) => [$r['name'] => $r['rates_count']])->filter(fn ($v) => $v > 0),
            'byStatus' => $byStatus,
            'monthly' => $monthly,
            'forecast' => $this->forecast($monthly, 3),
            'insights' => [],
        ];

        $insights = [];
        if ($summary['total_underwriters'] > 0) {
            $insights[] = sprintf(
                '%d of %d underwriters are available (active); %d have pricing configured (%d rate item(s) in total).',
                $active,
                $summary['total_underwriters'],
                $configured,
                $summary['total_rates']
            );
        }
        if ($inactive > 0) {
            $insights[] = sprintf('%d underwriter(s) are currently disabled and cannot be quoted against.', $inactive);
        }
        $unconfigured = $rows->filter(fn ($r) => $r['is_active'] && $r['rates_count'] === 0)->count();
        if ($unconfigured > 0) {
            $insights[] = sprintf('%d active underwriter(s) have no rates configured — availability may be limited.', $unconfigured);
        }
        if ($byCompany->isNotEmpty()) {
            $topName = $byCompany->keys()->first();
            $top = $byCompany->first();
            $insights[] = sprintf(
                '%s leads the portfolio with %d record(s) and %.1f%% of total premium (KES %s).',
                $topName,
                $top['count'],
                $top['share'],
                number_format($top['total_premium'], 2)
            );
        }
        $insights[] = sprintf(
            'Projected activity for %s is approximately %s record(s).',
            $analytics['forecast']['labels'][0] ?? 'the next period',
            number_format($analytics['forecast']['counts'][0] ?? 0, 0)
        );
        $analytics['insights'] = $insights;

        return ['underwriters' => $sorted, 'analytics' => $analytics];
    }

    public function userReport(Request $request)
    {
        return response()->json($this->buildUserReport($request));
    }

    public function underwriterReport(Request $request)
    {
        return response()->json($this->buildUnderwriterReport($request));
    }

    public function exportUsersPdf(Request $request)
    {
        $data = $this->buildUserReport($request);

        return Pdf::loadView('pdf.users-report', [
            'users' => $data['users'],
            'analytics' => $data['analytics'],
            'request' => $request,
            'unit' => 'Users',
        ])->setPaper('a4', 'landscape')->download('users-report-' . now()->format('Ymd_His') . '.pdf');
    }

    public function previewUsersPdf(Request $request)
    {
        $data = $this->buildUserReport($request);

        return Pdf::loadView('pdf.users-report', [
            'users' => $data['users'],
            'analytics' => $data['analytics'],
            'request' => $request,
            'unit' => 'Users',
        ])->setPaper('a4', 'landscape')->stream('users-report-preview.pdf');
    }

    public function exportUnderwritersPdf(Request $request)
    {
        $data = $this->buildUnderwriterReport($request);

        return Pdf::loadView('pdf.underwriters-report', [
            'underwriters' => $data['underwriters'],
            'analytics' => $data['analytics'],
            'request' => $request,
            'unit' => 'Underwriters',
        ])->setPaper('a4', 'landscape')->download('underwriters-report-' . now()->format('Ymd_His') . '.pdf');
    }

    public function previewUnderwritersPdf(Request $request)
    {
        $data = $this->buildUnderwriterReport($request);

        return Pdf::loadView('pdf.underwriters-report', [
            'underwriters' => $data['underwriters'],
            'analytics' => $data['analytics'],
            'request' => $request,
            'unit' => 'Underwriters',
        ])->setPaper('a4', 'landscape')->stream('underwriters-report-preview.pdf');
    }

    public function exportUsersCsv(Request $request)
    {
        $users = $this->buildUserReport($request)['users'];
        $filename = 'users_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Username', 'Branch', 'Role', 'Status', 'Quotations', 'Certificates', 'Total Premium (KES)', 'Conversion (%)', 'Last Online']);

            foreach ($users as $u) {
                fputcsv($handle, [
                    $u['username'],
                    $u['branch'] ?? '',
                    $u['roles'],
                    $u['is_active'] ? 'Active' : 'Inactive',
                    $u['quotations_count'],
                    $u['stickers_count'],
                    number_format($u['total_premium'], 2),
                    $u['conversion'],
                    $u['last_online_at'] ?? '—',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportUnderwritersCsv(Request $request)
    {
        $underwriters = $this->buildUnderwriterReport($request)['underwriters'];
        $filename = 'underwriters_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($underwriters) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Underwriter', 'Status', 'Rates Configured', 'Categories', 'Min SI PSV (KES)', 'Min SI Private (KES)', 'Quotations', 'Certificates', 'Total Premium (KES)']);

            foreach ($underwriters as $u) {
                fputcsv($handle, [
                    $u['name'],
                    $u['is_active'] ? 'Active' : 'Inactive',
                    $u['rates_count'],
                    $u['categories'],
                    number_format($u['min_sum_insured_psv'], 2),
                    number_format($u['min_sum_insured_private'], 2),
                    $u['quotations_count'],
                    $u['stickers_count'],
                    number_format($u['total_premium'], 2),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportUsersExcel(Request $request)
    {
        $users = $this->buildUserReport($request)['users'];
        $filePath = storage_path('app/users_' . now()->format('Ymd_His') . '.xlsx');

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($filePath);
        $writer->getCurrentSheet()->setName('Users');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Username', 'Branch', 'Role', 'Status', 'Quotations', 'Certificates', 'Total Premium (KES)', 'Conversion (%)', 'Last Online',
        ]));

        foreach ($users as $u) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $u['username'],
                $u['branch'] ?? '',
                $u['roles'],
                $u['is_active'] ? 'Active' : 'Inactive',
                $u['quotations_count'],
                $u['stickers_count'],
                $u['total_premium'],
                $u['conversion'],
                $u['last_online_at'] ?? '—',
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportUnderwritersExcel(Request $request)
    {
        $underwriters = $this->buildUnderwriterReport($request)['underwriters'];
        $filePath = storage_path('app/underwriters_' . now()->format('Ymd_His') . '.xlsx');

        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($filePath);
        $writer->getCurrentSheet()->setName('Underwriters');

        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            'Underwriter', 'Status', 'Rates Configured', 'Categories', 'Min SI PSV (KES)', 'Min SI Private (KES)', 'Quotations', 'Certificates', 'Total Premium (KES)',
        ]));

        foreach ($underwriters as $u) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                $u['name'],
                $u['is_active'] ? 'Active' : 'Inactive',
                $u['rates_count'],
                $u['categories'],
                $u['min_sum_insured_psv'],
                $u['min_sum_insured_private'],
                $u['quotations_count'],
                $u['stickers_count'],
                $u['total_premium'],
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
