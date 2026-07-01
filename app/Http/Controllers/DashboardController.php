<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\Company;
use App\Models\HealthCheck;
use App\Models\Quotation;
use App\Models\QuoteSession;
use App\Models\Sticker;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        // $weather = $this->weatherService->getWeather(
        //     config('app.weather_city', 'Nairobi'),
        //     config('app.weather_country', 'KE')
        // );
        $weather = app(\App\Services\WeatherService::class)->getWeather(
            session('weather_city', 'Nairobi')
        );

        $time = now();
        $hour = $time->hour;
        $greeting = match (true) {
            $hour < 12 => 'Good Morning',
            $hour < 17 => 'Good Afternoon',
            $hour < 21 => 'Good Evening',
            default => 'Good Night',
        };

        $data = [
            'greeting' => $greeting,
            'weather' => $weather,
            'time' => $time->format('h:i A'),
            'date' => $time->format('l, F j, Y'),
        ];

        if (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')) {
            return $this->adminDashboard($data);
        }

        return $this->userDashboard($data);
    }

    protected function adminDashboard(array $data)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $data['isSuperAdmin'] = $isSuperAdmin;
        $data['totalUsers'] = User::count();
        $data['activeUsers'] = User::where('is_active', true)->count();
        $data['onlineUsers'] = User::where('last_online_at', '>=', now()->subMinutes(5))->count();
        $data['totalQuotations'] = Quotation::count();
        $data['todayQuotations'] = Quotation::whereDate('created_at', today())->count();
        $data['weekQuotations'] = Quotation::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $data['monthQuotations'] = Quotation::whereMonth('created_at', now()->month)->count();
        $data['companies'] = Company::active()->withCount('quotations')->get();
        $data['totalPremium'] = Quotation::sum('total_premium');
        $data['recentQuotations'] = Quotation::with(['user', 'company'])->latest()->take(10)->get();

        $data['quotationsByCompany'] = Company::active()
            ->withCount('quotations')
            ->get()
            ->pluck('quotations_count', 'name');

        $data['monthlyQuotations'] = Quotation::select(
            DB::raw("strftime('%Y-%m', created_at) as month"),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data['usersByGender'] = User::select('gender', DB::raw('count(*) as total'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender');

        $companyColorMap = Company::active()->get()->mapWithKeys(function ($c) {
            $colors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];
            $idx = abs(crc32($c->id)) % count($colors);
            return [$c->name => $colors[$idx]];
        });

        $data['quotationDates'] = Quotation::with(['user', 'company'])
            ->latest()
            ->get()
            ->groupBy(fn($q) => $q->created_at->format('Y-m-d'))
            ->map(fn($items, $date) => [
                'date' => $date,
                'count' => $items->count(),
                'items' => $items->map(fn($q) => [
                    'id' => $q->id,
                    'quotation_number' => $q->quotation_number,
                    'user_name' => $q->user->name ?? 'Unknown',
                    'car' => $q->car_make . ' ' . $q->car_model,
                    'premium' => $q->total_premium,
                    'company' => $q->company->name ?? 'Unknown',
                    'company_color' => $companyColorMap[$q->company->name] ?? '#1756c8',
                ]),
            ]);

        $data['companyColorMap'] = $companyColorMap;

        // Status distribution
        $statusDistribution = [
            'processed' => \App\Models\Quotation::where('status', 'processed')->count(),
            'completed' => \App\Models\Quotation::where('status', 'completed')->count(),
            'rejected'  => \App\Models\Quotation::where('status', 'rejected')->count(),
        ];

        // Monthly average premium trend (last 6 months)
        $dbDriver = DB::connection()->getDriverName();
        if ($dbDriver === 'sqlite') {
            $monthExpr = "strftime('%b %Y', created_at) as month";
            $sortExpr = "strftime('%Y-%m', created_at) as sort_key";
        } else {
            $monthExpr = "DATE_FORMAT(created_at, '%b %Y') as month";
            $sortExpr = "DATE_FORMAT(created_at, '%Y-%m') as sort_key";
        }
        $premiumTrend = \App\Models\Quotation::selectRaw(
                "$monthExpr, $sortExpr, ROUND(AVG(total_premium), 0) as avg_premium"
            )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $data['premiumTrend'] = $premiumTrend;

        $data['allActivities'] = ActivityLog::with('user')->latest()->take(20)->get();
        $data['myActivities'] = ActivityLog::where('user_id', auth()->id())->latest()->take(10)->get();
        $data['unreadNotifications'] = auth()->user()->unreadNotifications()->latest()->take(5)->get();
        $data['unreadCount'] = auth()->user()->unreadNotifications()->count();
        $data['recentAlerts'] = AuditLog::with('user')->latest()->take(5)->get();

        $data['abandonedQuoteSessions'] = QuoteSession::with('user')
            ->where('status', 'started')
            ->where('started_at', '<', now()->subMinutes(30))
            ->latest('started_at')
            ->take(10)
            ->get();

        $data['abandonedQuoteCount'] = QuoteSession::where('status', 'started')
            ->where('started_at', '<', now()->subMinutes(30))
            ->count();

        $data['healthChecks'] = HealthCheck::latestByType()
            ->latest('checked_at')
            ->get();

        if ($isSuperAdmin) {
            $data['pendingChangeRequests'] = ChangeRequest::with('user')
                ->pending()
                ->latest()
                ->take(10)
                ->get();
            $data['pendingChangeCount'] = ChangeRequest::pending()->count();
        } else {
            $data['pendingChangeRequests'] = ChangeRequest::with('user')
                ->forUser($user->id)
                ->latest()
                ->take(5)
                ->get();
            $data['pendingChangeCount'] = ChangeRequest::forUser($user->id)->pending()->count();
        }

        return view($isSuperAdmin ? 'dashboard.super-admin' : 'dashboard.admin', $data);
    }

    protected function userDashboard(array $data)
    {
        $user = auth()->user();

        $data['memberSince'] = $user->created_at->format('F Y');

        $data['stickersCount'] = Sticker::whereIn('quotation_id', $user->quotations()->pluck('id'))->count();

        $filledFields = collect([$user->first_name, $user->last_name, $user->email, $user->mobile, $user->gender, $user->avatar])->filter(fn($v) => !is_null($v) && $v !== '')->count();
        $data['profileCompletion'] = round(($filledFields / 6) * 100);

        $data['recentActivities'] = ActivityLog::where('user_id', $user->id)->latest()->take(10)->get();

        $companyColorMap = Company::active()->get()->mapWithKeys(function ($c) {
            $colors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];
            $idx = abs(crc32($c->id)) % count($colors);
            return [$c->name => $colors[$idx]];
        });

        $data['quotationDates'] = $user->quotations()
            ->with('company')
            ->select('quotation_number', 'car_make', 'car_model', 'total_premium', 'created_at', 'company_id')
            ->latest()
            ->get()
            ->groupBy(fn($q) => $q->created_at->format('Y-m-d'))
            ->map(fn($items, $date) => [
                'date' => $date,
                'count' => $items->count(),
                'items' => $items->map(fn($q) => [
                    'id' => $q->id,
                    'quotation_number' => $q->quotation_number,
                    'user_name' => $q->user->name ?? 'Unknown',
                    'car' => $q->car_make . ' ' . $q->car_model,
                    'premium' => $q->total_premium,
                    'company' => $q->company->name ?? 'Unknown',
                    'company_color' => $companyColorMap[$q->company->name] ?? '#1756c8',
                ]),
            ]);

        $data['companyColorMap'] = $companyColorMap;

        $data['quotationsByCompany'] = Company::active()
            ->withCount(['quotations' => fn($q) => $q->where('user_id', $user->id)])
            ->get()
            ->pluck('quotations_count', 'name');

        $data['monthlyQuotations'] = $user->quotations()
            ->select(DB::raw("strftime('%Y-%m', created_at) as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data['weeklyQuotations'] = $user->quotations()
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $data['monthQuotations'] = $user->quotations()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $data['recentQuotations'] = $user->quotations()->with('company')->latest()->take(5)->get();

        $data['quotations'] = $user->quotations()->with('company')->latest()->take(5)->get();
        $data['totalQuotations'] = $user->quotations()->count();
        $data['todayQuotations'] = $user->quotations()->whereDate('created_at', today())->count();
        $data['companies'] = Company::active()->get();
        $data['recentQuotation'] = $user->quotations()->latest()->first();
        $data['recentNotifications'] = $user->unreadNotifications()->latest()->take(5)->get();
        $data['unreadCount'] = $user->unreadNotifications()->count();

        // ── APPEND BELOW YOUR EXISTING LINES, RIGHT BEFORE `return view(...)` ──
        // Nothing above this needs to change. These reuse $user, $companyColorMap,
        // and $data['totalQuotations'] that you already compute.
        // C1. Status breakdown (pie chart)
        $data['quotationStatusBreakdown'] = $user->quotations()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        // C2. Quotations by company (bar chart) — alias of what you already build
        $data['companyQuotationCounts'] = $data['quotationsByCompany'];
        // C3. Quotations monthly (line chart) — reshape monthlyQuotations into
        // a "label => count" map the chart can consume directly (e.g. "Jul 2026" => 4)
        $data['quotationsByMonth'] = $data['monthlyQuotations']
            ->mapWithKeys(fn($m) => [
                \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y') => $m->total,
            ]);
        // C4 + Table. One query (withCount + withSum) covering premium comparison
        // and the company comparison table — same pattern as $data['quotationsByCompany']
        $companyStats = Company::active()
            ->withCount(['quotations as user_quotations_count' => fn($q) => $q->where('user_id', $user->id)])
            ->withSum(['quotations as user_premium_sum' => fn($q) => $q->where('user_id', $user->id)], 'total_premium')
            ->get()
            ->filter(fn($c) => $c->user_quotations_count > 0);
        $data['companyPremiumTotals'] = $companyStats
            ->mapWithKeys(fn($c) => [$c->name => (float) ($c->user_premium_sum ?? 0)]);
        $totalUserQuotations = $data['totalQuotations'];
        $data['companyComparison'] = $companyStats
            ->map(fn($c) => [
                'company' => $c->name,
                'total_quotations' => $c->user_quotations_count,
                'total_premium' => (float) ($c->user_premium_sum ?? 0),
                'avg_premium' => $c->user_quotations_count > 0
                    ? ($c->user_premium_sum ?? 0) / $c->user_quotations_count
                    : 0,
                'market_share' => $totalUserQuotations > 0
                    ? ($c->user_quotations_count / $totalUserQuotations) * 100
                    : 0,
            ])
            ->sortByDesc('total_quotations')
            ->values()
            ->all();

        return view('dashboard.user', $data);
    }

    public function weatherJson()
    {
        $weather = $this->weatherService->getWeather(
            config('app.weather_city', 'Nairobi'),
            config('app.weather_country', 'KE')
        );
        return response()->json($weather);
    }
}
