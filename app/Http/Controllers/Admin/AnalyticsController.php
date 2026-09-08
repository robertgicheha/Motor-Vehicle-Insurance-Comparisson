<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Company;
use App\Models\Sticker;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalQuotations = Quotation::count();
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $totalStickers = Sticker::count();

        // Grouped in PHP (not via a driver-specific SQL date function like
        // strftime/DATE_FORMAT) so this never breaks depending on which
        // database driver is actually active.
        $monthlyQuotations = Quotation::where('created_at', '>=', now()->subMonths(12))
            ->pluck('created_at')
            ->countBy(fn($createdAt) => $createdAt->format('Y-m'))
            ->sortKeys()
            ->map(fn($total, $month) => (object)['month' => $month, 'total' => $total])
            ->values();

        $companyBreakdown = Company::active()
            ->withCount('quotations')
            ->get()
            ->pluck('quotations_count', 'name');

        $quotationsByStatus = Quotation::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(20)
            ->get();

        $topUsers = User::withCount('quotations')
            ->orderBy('quotations_count', 'desc')
            ->take(10)
            ->get();

        $stickerStats = Sticker::where('created_at', '>=', now()->subMonths(12))
            ->pluck('created_at')
            ->countBy(fn($createdAt) => $createdAt->format('Y-m'))
            ->sortKeys()
            ->map(fn($total, $month) => (object)['month' => $month, 'total' => $total])
            ->values();

        return view('admin.analytics.index', compact(
            'totalQuotations',
            'totalUsers',
            'totalCompanies',
            'totalStickers',
            'monthlyQuotations',
            'companyBreakdown',
            'quotationsByStatus',
            'recentActivities',
            'topUsers',
            'stickerStats'
        ));
    }
}
