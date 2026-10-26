<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Company;
use App\Models\Sticker;
use App\Models\Rate;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AnalyticsController extends Controller
{
    public function index()
    {
        $kpis = $this->kpis();

        // Grouped in PHP (not via a driver-specific SQL date function like
        // strftime/DATE_FORMAT) so this never breaks depending on which
        // database driver is actually active.
        $monthlyQuotations = $this->monthlySeries(Quotation::query(), 12);
        $stickerStats = $this->monthlySeries(Sticker::query(), 12);

        $companyBreakdown = Company::active()
            ->withCount('quotations')
            ->get()
            ->pluck('quotations_count', 'name');

        $quotationsByStatus = Quotation::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $recentActivities = $this->recentActivityFeed(25);

        $topUsers = User::withCount('quotations')
            ->orderBy('quotations_count', 'desc')
            ->take(10)
            ->get();

        // --- Underwriter availability / performance -------------------------
        $ratesByCompany = Rate::query()
            ->selectRaw('company_id, COUNT(*) as c')
            ->groupBy('company_id')
            ->pluck('c', 'company_id');

        $underwriterRows = Company::query()
            ->withCount(['quotations', 'stickers'])
            ->withSum('quotations as quotations_premium', 'total_premium')
            ->withSum('stickers as stickers_premium', 'premium')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'is_active' => (bool) $c->is_active,
                'rates_count' => (int) ($ratesByCompany[$c->id] ?? 0),
                'quotations_count' => (int) $c->quotations_count,
                'stickers_count' => (int) $c->stickers_count,
                'premium' => round((float) $c->quotations_premium + (float) $c->stickers_premium, 2),
            ]);

        // --- User distribution ---------------------------------------------
        $userStatus = [
            'active' => $kpis['active_users'],
            'inactive' => max(0, $kpis['total_users'] - $kpis['active_users']),
        ];

        $usersByRole = Role::withCount('users')->get()->pluck('users_count', 'name');

        $usersByBranch = User::all()
            ->groupBy(fn ($u) => $u->branch ?: 'Unassigned')
            ->map->count()
            ->sortDesc();

        $insights = $this->insights($kpis, $underwriterRows);

        return view('admin.analytics.index', compact(
            'kpis',
            'monthlyQuotations',
            'stickerStats',
            'companyBreakdown',
            'quotationsByStatus',
            'recentActivities',
            'topUsers',
            'underwriterRows',
            'userStatus',
            'usersByRole',
            'usersByBranch',
            'insights'
        ));
    }

    /**
     * Lightweight JSON snapshot for the real-time activity feed & KPI tiles.
     */
    public function live()
    {
        return response()->json([
            'generated_at' => now()->format('H:i:s'),
            'kpis' => $this->kpis(),
            'activities' => $this->recentActivityFeed(25),
        ]);
    }

    private function kpis(): array
    {
        $quotationCount = Quotation::count();
        $stickerCount = Sticker::count();
        $quotationPremium = (float) Quotation::sum('total_premium');
        $stickerPremium = (float) Sticker::sum('premium');

        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'online_users' => User::where('last_online_at', '>=', now()->subMinutes(5))->count(),
            'new_users_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_quotations' => $quotationCount,
            'quotations_month' => Quotation::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_stickers' => $stickerCount,
            'stickers_month' => Sticker::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'total_rates' => Rate::count(),
            'quotation_premium' => round($quotationPremium, 2),
            'sticker_premium' => round($stickerPremium, 2),
            'total_premium' => round($quotationPremium + $stickerPremium, 2),
            'total_downloads' => (int) Sticker::sum('download_count'),
            'conversion_rate' => $quotationCount > 0 ? round(($stickerCount / $quotationCount) * 100, 1) : 0.0,
        ];
    }

    private function monthlySeries($query, int $months = 12)
    {
        return $query->where('created_at', '>=', now()->subMonths($months))
            ->pluck('created_at')
            ->countBy(fn ($createdAt) => $createdAt->format('Y-m'))
            ->sortKeys()
            ->map(fn ($total, $month) => (object) ['month' => $month, 'total' => $total])
            ->values();
    }

    private function recentActivityFeed(int $limit = 25): array
    {
        return AuditLog::query()
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($log) => [
                'user' => $log->user_name ?: ($log->user?->username ?? 'System'),
                'action' => $log->action ?: 'activity',
                'model' => $log->model,
                'model_id' => $log->model_id,
                'ip' => $log->ip_address,
                'created_at' => $log->created_at?->toIso8601String(),
                'time' => $log->created_at?->diffForHumans(),
            ])
            ->all();
    }

    private function insights(array $k, $underwriterRows): array
    {
        $out = [];

        $out[] = sprintf(
            '%s quotations and %s stickers have been generated, a conversion rate of %.1f%%.',
            number_format($k['total_quotations']),
            number_format($k['total_stickers']),
            $k['conversion_rate']
        );

        $out[] = sprintf(
            'Total premium written stands at KES %s (KES %s from quotations, KES %s from stickers).',
            number_format($k['total_premium'], 2),
            number_format($k['quotation_premium'], 2),
            number_format($k['sticker_premium'], 2)
        );

        $out[] = sprintf(
            '%d of %d underwriters are active, with %s rate item(s) configured across the portfolio.',
            $k['active_companies'],
            $k['total_companies'],
            number_format($k['total_rates'])
        );

        $out[] = sprintf(
            '%d of %d user accounts are active and %d are online right now.',
            $k['active_users'],
            $k['total_users'],
            $k['online_users']
        );

        $top = collect($underwriterRows)->sortByDesc('premium')->first();
        if ($top && $top['premium'] > 0) {
            $out[] = sprintf('%s leads the portfolio with KES %s in written premium.', $top['name'], number_format($top['premium'], 2));
        }

        $out[] = sprintf(
            'This month: %s new quotation(s), %s new sticker(s) and %s new account(s).',
            number_format($k['quotations_month']),
            number_format($k['stickers_month']),
            number_format($k['new_users_month'])
        );

        return $out;
    }
}
