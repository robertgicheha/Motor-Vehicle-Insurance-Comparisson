<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\ChangeRequest;
use App\Models\Claim;
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
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
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
            'weather' => null,
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

        $companyColors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];

        $companyColorMap = Company::active()->get()->mapWithKeys(function ($c) use ($companyColors) {
            $idx = abs(crc32($c->id)) % count($companyColors);
            return [$c->name => $companyColors[$idx]];
        });
        $data['companyColorMap'] = $companyColorMap;

        $activeCompanies = Company::active()->withCount('quotations')->get();
        $data['companies'] = $activeCompanies;
        $data['totalCompanies'] = $activeCompanies->count();
        $data['quotationsByCompany'] = $activeCompanies->pluck('quotations_count', 'name');

        $todayStart = today();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $quotationStats = Quotation::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today_count,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week_count,
            SUM(CASE WHEN created_at >= ? AND created_at <= ? THEN 1 ELSE 0 END) as month_count,
            SUM(total_premium) as total_premium,
            SUM(CASE WHEN status = 'generated' THEN 1 ELSE 0 END) as generated_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
        ", [$todayStart, $weekStart, $monthStart, $monthEnd])->first();

        $data['totalQuotations'] = $quotationStats->total;
        $data['todayQuotations'] = $quotationStats->today_count;
        $data['weekQuotations'] = $quotationStats->week_count;
        $data['monthQuotations'] = $quotationStats->month_count;
        $data['totalPremium'] = $quotationStats->total_premium ?? 0;

        $userStats = User::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count,
            SUM(CASE WHEN last_online_at >= ? THEN 1 ELSE 0 END) as online_count
        ", [now()->subMinutes(5)])->first();

        $data['totalUsers'] = $userStats->total;
        $data['activeUsers'] = $userStats->active_count;
        $data['onlineUsers'] = $userStats->online_count;

        $data['recentQuotations'] = Quotation::with(['user:id,name', 'company:id,name'])
            ->latest()
            ->take(10)
            ->get();

        $data['monthlyQuotations'] = Quotation::select(
            DB::raw("strftime('%Y-%m', created_at) as month"),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $data['usersByGender'] = User::select('gender', DB::raw('count(*) as total'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender');

        $data['quotationDates'] = Quotation::with(['user:id,name', 'company:id,name'])
            ->latest()
            ->take(100)
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

        $dbDriver = DB::connection()->getDriverName();
        if ($dbDriver === 'sqlite') {
            $monthExpr = "strftime('%b %Y', created_at) as month";
            $sortExpr = "strftime('%Y-%m', created_at) as sort_key";
        } else {
            $monthExpr = "DATE_FORMAT(created_at, '%b %Y') as month";
            $sortExpr = "DATE_FORMAT(created_at, '%Y-%m') as sort_key";
        }
        $data['premiumTrend'] = Quotation::selectRaw(
                "$monthExpr, $sortExpr, ROUND(AVG(total_premium), 0) as avg_premium"
            )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $data['allActivities'] = ActivityLog::with('user:id,name,avatar')->latest()->take(20)->get();
        $data['myActivities'] = ActivityLog::where('user_id', $user->id)->latest()->take(10)->get();

        $data['unreadNotifications'] = $user->unreadNotifications()->latest()->take(5)->get();
        $data['unreadCount'] = $user->unreadNotifications()->count();

        $data['recentAlerts'] = AuditLog::with('user:id,name')->latest()->take(5)->get();

        $data['abandonedQuoteSessions'] = QuoteSession::with('user:id,name')
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
            ->take(10)
            ->get();

        if ($isSuperAdmin) {
            $data['pendingChangeRequests'] = ChangeRequest::with('user:id,name')
                ->pending()
                ->latest()
                ->take(10)
                ->get();
            $data['pendingChangeCount'] = ChangeRequest::pending()->count();
        } else {
            $data['pendingChangeRequests'] = ChangeRequest::with('user:id,name')
                ->forUser($user->id)
                ->latest()
                ->take(5)
                ->get();
            $data['pendingChangeCount'] = ChangeRequest::forUser($user->id)->pending()->count();
        }

        $data['totalClaims'] = Claim::count();
        $data['totalStickers'] = Sticker::count();
        $data['recentUsers'] = User::latest()->take(5)->get();
        $data['recentClaims'] = Claim::latest()->take(5)->get();

        $data = array_merge($data, $this->quoteData());

        return view($isSuperAdmin ? 'dashboard.super-admin' : 'dashboard.admin', $data);
    }

    protected function quoteData(): array
    {
        $companyColors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];

        $prepareCompanyData = function ($companiesForType, $vehicleType) use ($companyColors) {
            return $companiesForType->values()->map(function ($c, $i) use ($companyColors, $vehicleType) {
                $rate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Premium Rate');
                $pvtRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PVT Rate');
                $pllRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'PLL Per Person');
                $maxAgeRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Max Age New Business');
                $excessProtectorRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Excess Protector');
                $geoAreaRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Geographical Area');
                $minSumInsuredRate = $c->rates->where('vehicle_type', $vehicleType)->firstWhere('item_name', 'Minimum Sum Insured');
                $minSumInsured = $vehicleType === 'psv' ? (float) $c->min_sum_insured_psv : (float) $c->min_sum_insured_private;
                return [
                    'slug' => $c->slug,
                    'name' => $c->name,
                    'logo' => $c->logoUrl(),
                    'rate' => $rate ? (float) $rate->percentage / 100 : 0,
                    'taxRate' => 0,
                    'minPremium' => $rate ? (float) $rate->min_premium : 0,
                    'isPvtInclusive' => $rate ? (bool) $rate->is_pvt_inclusive : false,
                    'pvtRate' => $pvtRate ? (float) $pvtRate->percentage / 100 : 0,
                    'pvtMinValue' => $pvtRate ? (float) $pvtRate->min_value : 0,
                    'pllPerPerson' => $pllRate ? (float) $pllRate->value : 0,
                    'maxAge' => $maxAgeRate ? (int) $maxAgeRate->value : 99,
                    'minSumInsured' => $minSumInsured,
                    'minSumInsuredText' => $minSumInsured ? 'KES ' . number_format($minSumInsured, 0) : ($minSumInsuredRate?->notes ?? '—'),
                    'maxAgeText' => $maxAgeRate ? $maxAgeRate->value . ' years' : '—',
                    'excessProtector' => $excessProtectorRate?->notes ?? null,
                    'geoArea' => $geoAreaRate?->notes ?? ($geoAreaRate?->value ? 'KES ' . number_format($geoAreaRate->value, 0) : null),
                    'color' => $companyColors[$i % count($companyColors)],
                ];
            })->values();
        };

        $allActiveCompanies = Company::active()->with('rates')->get();
        $privateCompanies = $allActiveCompanies->filter(fn($c) => in_array($c->company_type, ['private', 'both']));
        $psvCompanies = $allActiveCompanies->filter(fn($c) => in_array($c->company_type, ['psv', 'both']));

        return [
            'quoteCompanies' => $allActiveCompanies,
            'quoteCompanyDataPrivate' => $prepareCompanyData($privateCompanies, 'private'),
            'quoteCompanyDataPsv' => $prepareCompanyData($psvCompanies, 'psv'),
            'quoteCompanyColors' => $companyColors,
        ];
    }

    protected function userDashboard(array $data)
    {
        $user = auth()->user();
        $userId = $user->id;

        $data['memberSince'] = $user->created_at->format('F Y');

        $companyColors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b', '#1E40AF', '#F59E0B', '#10B981', '#8B5CF6', '#EF4444'];
        $companyColorMap = Company::active()->get()->mapWithKeys(function ($c) use ($companyColors) {
            $idx = abs(crc32($c->id)) % count($companyColors);
            return [$c->name => $companyColors[$idx]];
        });
        $data['companyColorMap'] = $companyColorMap;

        $userQuotationIds = $user->quotations()->pluck('id');

        $data['stickersCount'] = Sticker::whereIn('quotation_id', $userQuotationIds)->count();

        $claimQuery = function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('on_behalf_of_user_id', $userId);
        };
        $data['myClaimsCount'] = Claim::where($claimQuery)->count();
        $data['myOpenClaimsCount'] = Claim::where($claimQuery)
            ->whereIn('status', ['reported', 'documents_pending', 'under_review', 'settlement', 'under_investigation'])
            ->count();

        $data['recentMyClaims'] = Claim::with(['user:id,name', 'onBehalfOfUser:id,name'])
            ->where($claimQuery)
            ->latest()
            ->take(4)
            ->get();

        $filledFields = collect([$user->first_name, $user->last_name, $user->email, $user->mobile, $user->gender, $user->avatar])->filter(fn($v) => !is_null($v) && $v !== '')->count();
        $data['profileCompletion'] = round(($filledFields / 6) * 100);

        $data['recentActivities'] = ActivityLog::where('user_id', $userId)->latest()->take(10)->get();

        $data['quotationDates'] = $user->quotations()
            ->with('company:id,name')
            ->select('quotation_number', 'car_make', 'car_model', 'total_premium', 'created_at', 'company_id')
            ->latest()
            ->take(50)
            ->get()
            ->groupBy(fn($q) => $q->created_at->format('Y-m-d'))
            ->map(fn($items, $date) => [
                'date' => $date,
                'count' => $items->count(),
                'items' => $items->map(fn($q) => [
                    'id' => $q->id,
                    'quotation_number' => $q->quotation_number,
                    'car' => $q->car_make . ' ' . $q->car_model,
                    'premium' => $q->total_premium,
                    'company' => $q->company->name ?? 'Unknown',
                    'company_color' => $companyColorMap[$q->company->name] ?? '#1756c8',
                ]),
            ]);

        $companyUserStats = Company::active()
            ->withCount(['quotations as user_quotations_count' => fn($q) => $q->where('user_id', $userId)])
            ->withSum(['quotations as user_premium_sum' => fn($q) => $q->where('user_id', $userId)], 'total_premium')
            ->get();

        $data['quotationsByCompany'] = $companyUserStats->pluck('user_quotations_count', 'name');

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

        $data['recentQuotations'] = $user->quotations()->with('company:id,name')->latest()->take(5)->get();
        $data['totalQuotations'] = $userQuotationIds->count();
        $data['todayQuotations'] = $user->quotations()->whereDate('created_at', today())->count();
        $data['companies'] = Company::active()->get();
        $data['recentQuotation'] = $user->quotations()->latest()->first();
        $data['recentNotifications'] = $user->unreadNotifications()->latest()->take(5)->get();
        $data['unreadCount'] = $user->unreadNotifications()->count();

        $data = array_merge($data, $this->quoteData());

        $data['quotationStatusBreakdown'] = $user->quotations()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $data['companyQuotationCounts'] = $data['quotationsByCompany'];
        $data['quotationsByMonth'] = $data['monthlyQuotations']
            ->mapWithKeys(fn($m) => [
                \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y') => $m->total,
            ]);

        $companyStats = $companyUserStats->filter(fn($c) => $c->user_quotations_count > 0);
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

    public function weatherJson(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if ($lat && $lon) {
            $weather = $this->weatherService->getWeatherByCoords($lat, $lon);

            if ($weather && !empty($weather['city'])) {
                session(['weather_city' => $weather['city']]);
            }
        } else {
            $weather = $this->weatherService->getWeatherByIp();
        }

        return response()->json($weather);
    }
}
