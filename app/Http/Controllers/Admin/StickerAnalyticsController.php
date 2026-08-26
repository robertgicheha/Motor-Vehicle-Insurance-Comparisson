<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Sticker;
use App\Models\StickerEmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StickerAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $query = Sticker::with('company');
        $this->applyFilters($query, $request);
        $stickers = $query->get();

        $total = $stickers->count();

        $byCompany = $stickers->groupBy('company.name')->map(function ($items, $name) {
            $count = $items->count();
            return [
                'name' => $name,
                'count' => $count,
                'total_premium' => $items->sum('premium'),
                'avg_premium' => $items->avg('premium'),
                'total_downloads' => $items->sum('download_count'),
                'active' => $items->where('status', 'completed')->count(),
                'pending' => $items->where('status', 'issued')->count(),
                'share_pct' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('count')->values();

        $byMonth = $stickers->groupBy(function ($s) {
            return $s->created_at->format('Y-m');
        })->map(function ($items, $month) {
            return [
                'month' => $month,
                'label' => \Carbon\Carbon::parse($month . '-01')->format('M Y'),
                'count' => $items->count(),
                'premium' => $items->sum('premium'),
            ];
        })->sortBy('month')->values();

        $byDay = $stickers->groupBy(function ($s) {
            return $s->created_at->format('Y-m-d');
        })->map(function ($items, $date) {
            return [
                'date' => $date,
                'count' => $items->count(),
                'premium' => $items->sum('premium'),
            ];
        })->sortBy('date')->values();

        $byCoverType = $stickers->groupBy('cover_type')->map(function ($items, $type) {
            return [
                'type' => ucfirst($type ?? 'Unknown'),
                'count' => $items->count(),
                'premium' => $items->sum('premium'),
            ];
        })->values();

        $byStatus = $stickers->groupBy('status')->map(function ($items, $status) {
            return [
                'status' => $status,
                'count' => $items->count(),
            ];
        })->values();

        $byCreatedVia = $stickers->groupBy('created_via')->map(function ($items, $via) {
            return [
                'via' => strtoupper($via ?? 'web'),
                'count' => $items->count(),
            ];
        })->values();

        $emailStats = [
            'sent' => StickerEmailLog::where('type', 'outbound_to_underwriter')->where('status', 'sent')->count(),
            'failed' => StickerEmailLog::where('type', 'outbound_to_underwriter')->whereIn('status', ['failed', 'bounced'])->count(),
            'pending' => Sticker::where('status', 'issued')->whereNull('response_received_at')->count(),
            'breached' => Sticker::where('status', 'issued')
                ->whereNotNull('email_sent_at')
                ->whereNull('response_received_at')
                ->whereRaw('TIMESTAMPDIFF(MINUTE, email_sent_at, NOW()) > ?', [config('services.sticker.turnaround_minutes', 30)])
                ->count(),
        ];

        $topCompany = $byCompany->first();
        $totalPremium = $stickers->sum('premium');
        $totalDownloads = $stickers->sum('download_count');

        $companies = Company::active()->get();

        return view('admin.stickers.analytics', compact(
            'stickers', 'byCompany', 'byMonth', 'byDay', 'byCoverType', 'byStatus', 'byCreatedVia',
            'emailStats', 'total', 'totalPremium', 'totalDownloads', 'topCompany', 'companies', 'request'
        ));
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('period')) {
            match ($request->period) {
                'today' => $query->whereDate('created_at', today()),
                'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'quarter' => $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]),
                'year' => $query->whereYear('created_at', now()->year),
                default => null,
            };
        }
    }
}
