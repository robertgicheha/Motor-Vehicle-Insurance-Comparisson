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
                ]),
            ]);

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

        return view('dashboard.admin', $data);
    }

    protected function userDashboard(array $data)
    {
        $user = auth()->user();

        $data['memberSince'] = $user->created_at->format('F Y');

        $data['stickersCount'] = Sticker::whereIn('quotation_id', $user->quotations()->pluck('id'))->count();

        $filledFields = collect([$user->first_name, $user->last_name, $user->email, $user->mobile, $user->gender, $user->avatar])->filter(fn($v) => !is_null($v) && $v !== '')->count();
        $data['profileCompletion'] = round(($filledFields / 6) * 100);

        $data['recentActivities'] = ActivityLog::where('user_id', $user->id)->latest()->take(10)->get();

        $data['quotationDates'] = $user->quotations()
            ->select('quotation_number', 'car_make', 'car_model', 'total_premium', 'created_at')
            ->latest()
            ->get()
            ->groupBy(fn($q) => $q->created_at->format('Y-m-d'))
            ->map(fn($items, $date) => ['date' => $date, 'count' => $items->count(), 'items' => $items]);

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

        return view('dashboard.user', $data);
    }
}
