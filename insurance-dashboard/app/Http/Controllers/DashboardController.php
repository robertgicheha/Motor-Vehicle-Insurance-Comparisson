<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\User;
use App\Models\Company;
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
        $weather = $this->weatherService->getWeather(
            config('app.weather_city', 'Nairobi'),
            config('app.weather_country', 'KE')
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

        return view('dashboard.admin', $data);
    }

    protected function userDashboard(array $data)
    {
        $user = auth()->user();
        $data['quotations'] = $user->quotations()->with('company')->latest()->take(5)->get();
        $data['totalQuotations'] = $user->quotations()->count();
        $data['todayQuotations'] = $user->quotations()->whereDate('created_at', today())->count();
        $data['companies'] = Company::active()->get();
        $data['recentQuotation'] = $user->quotations()->latest()->first();

        return view('dashboard.user', $data);
    }
}
