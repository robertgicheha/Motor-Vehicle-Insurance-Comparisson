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

        $dbDriver = DB::connection()->getDriverName();
        $monthExpr = $dbDriver === 'sqlite'
            ? "strftime('%Y-%m', created_at) as month"
            : "DATE_FORMAT(created_at, '%Y-%m') as month";

        $monthlyQuotations = Quotation::select(
            DB::raw($monthExpr),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

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

        $stickerStats = Sticker::select(
            DB::raw($monthExpr),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

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
