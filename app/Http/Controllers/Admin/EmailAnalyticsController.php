<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClaimEmailLog;
use App\Models\Company;
use App\Models\StickerEmailLog;
use Carbon\Carbon;

class EmailAnalyticsController extends Controller
{
    protected function isSuccessful(string $status): bool
    {
        return in_array($status, ['sent', 'delivered', 'opened', 'clicked']);
    }

    public function index()
    {
        $claimLogs = ClaimEmailLog::with('claim')->get();
        $stickerLogs = StickerEmailLog::with('sticker.company')->get();

        $totalSent = $claimLogs->count() + $stickerLogs->count();
        $totalSuccessful = $claimLogs->filter(fn($l) => $this->isSuccessful($l->status))->count()
            + $stickerLogs->filter(fn($l) => $this->isSuccessful($l->status))->count();
        $totalFailed = $totalSent - $totalSuccessful;
        $successRate = $totalSent > 0 ? round(($totalSuccessful / $totalSent) * 100, 1) : 0;

        // Which underwriter is getting the most emails — claims and stickers
        // combined by insurer name. Claims store their company only inside
        // vehicle_details JSON (no direct relation), so it's resolved once
        // per unique id via a lookup map rather than N+1 queries.
        $companyIds = $claimLogs->pluck('claim.vehicle_details.insurance_company_id')->filter()->unique();
        $companiesById = Company::whereIn('id', $companyIds)->get()->keyBy('id');

        $byUnderwriter = [];
        foreach ($claimLogs as $log) {
            $companyId = data_get($log->claim, 'vehicle_details.insurance_company_id');
            $name = $companyId && $companiesById->has($companyId) ? $companiesById[$companyId]->name : 'Unknown';
            $byUnderwriter[$name]['claims_sent'] = ($byUnderwriter[$name]['claims_sent'] ?? 0) + 1;
            $byUnderwriter[$name]['claims_successful'] = ($byUnderwriter[$name]['claims_successful'] ?? 0) + ($this->isSuccessful($log->status) ? 1 : 0);
        }
        foreach ($stickerLogs as $log) {
            $name = $log->sticker?->company?->name ?? 'Unknown';
            $byUnderwriter[$name]['stickers_sent'] = ($byUnderwriter[$name]['stickers_sent'] ?? 0) + 1;
            $byUnderwriter[$name]['stickers_successful'] = ($byUnderwriter[$name]['stickers_successful'] ?? 0) + ($this->isSuccessful($log->status) ? 1 : 0);
        }
        $underwriterRows = collect($byUnderwriter)->map(function ($row, $name) {
            $claimsSent = $row['claims_sent'] ?? 0;
            $stickersSent = $row['stickers_sent'] ?? 0;
            return [
                'name' => $name,
                'claims_sent' => $claimsSent,
                'claims_successful' => $row['claims_successful'] ?? 0,
                'stickers_sent' => $stickersSent,
                'stickers_successful' => $row['stickers_successful'] ?? 0,
                'total_sent' => $claimsSent + $stickersSent,
            ];
        })->sortByDesc('total_sent')->values();

        $topClaimsUnderwriter = $underwriterRows->sortByDesc('claims_sent')->first();

        // Time-bucketed counts (combined claims + stickers), based on sent_at
        // when the send actually happened, falling back to created_at for
        // rows that never got a sent_at stamp (e.g. failed sends).
        $allLogs = $claimLogs->map(fn($l) => ['at' => $l->sent_at ?? $l->created_at])
            ->concat($stickerLogs->map(fn($l) => ['at' => $l->sent_at ?? $l->created_at]));

        $now = Carbon::now();
        $buckets = [
            'today' => $allLogs->filter(fn($l) => $l['at'] && $l['at']->isToday())->count(),
            'this_week' => $allLogs->filter(fn($l) => $l['at'] && $l['at']->greaterThanOrEqualTo($now->copy()->startOfWeek()))->count(),
            'this_month' => $allLogs->filter(fn($l) => $l['at'] && $l['at']->greaterThanOrEqualTo($now->copy()->startOfMonth()))->count(),
            'last_6_months' => $allLogs->filter(fn($l) => $l['at'] && $l['at']->greaterThanOrEqualTo($now->copy()->subMonths(6)))->count(),
            'this_year' => $allLogs->filter(fn($l) => $l['at'] && $l['at']->greaterThanOrEqualTo($now->copy()->startOfYear()))->count(),
        ];

        $recentLogs = $claimLogs->map(fn($l) => [
                'type' => 'Claim',
                'recipient' => $l->recipient,
                'subject' => $l->subject,
                'status' => $l->status,
                'at' => $l->sent_at ?? $l->created_at,
            ])
            ->concat($stickerLogs->map(fn($l) => [
                'type' => 'Sticker',
                'recipient' => $l->recipient,
                'subject' => $l->subject,
                'status' => $l->status,
                'at' => $l->sent_at ?? $l->created_at,
            ]))
            ->sortByDesc('at')
            ->take(30)
            ->values();

        return view('admin.email-analytics.index', compact(
            'totalSent', 'totalSuccessful', 'totalFailed', 'successRate',
            'underwriterRows', 'topClaimsUnderwriter', 'buckets', 'recentLogs'
        ));
    }
}
