<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    public function getWeather($city = 'Nairobi', $country = 'KE')
    {
        $cacheKey = "weather_{$city}_{$country}";
        return Cache::remember($cacheKey, 1800, function () use ($city, $country) {
            $apiKey = config('services.openweather.key');
            if (!$apiKey) {
                return $this->fallbackWeather();
            }
            try {
                $response = Http::timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => "{$city},{$country}",
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'temperature' => round($data['main']['temp']),
                        'feels_like' => round($data['main']['feels_like']),
                        'condition' => $data['weather'][0]['main'],
                        'description' => $data['weather'][0]['description'],
                        'icon' => $data['weather'][0]['icon'],
                        'humidity' => $data['main']['humidity'],
                        'city' => $data['name'],
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('Weather API failed: ' . $e->getMessage());
            }
            return $this->fallbackWeather();
        });
    }

    private function fallbackWeather()
    {
        return [
            'temperature' => 25,
            'feels_like' => 24,
            'condition' => 'Clear',
            'description' => 'clear sky',
            'icon' => '01d',
            'humidity' => 60,
            'city' => 'Nairobi',
        ];
    }
}
