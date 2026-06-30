<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
   protected array $coords = [
        'Nairobi' => ['lat' => -1.2921, 'lon' => 36.8219, 'tz' => 'Africa/Nairobi'],
        'Mombasa' => ['lat' => -4.0435, 'lon' => 39.6682, 'tz' => 'Africa/Nairobi'],
        'Kisumu'  => ['lat' => -0.0917, 'lon' => 34.7679, 'tz' => 'Africa/Nairobi'],
    ];

    public function getWeather($city = 'Nairobi', $country = 'KE')
    {
        $cacheKey = "weather_v2_{$city}";

        return Cache::remember($cacheKey, 300, function () use ($city) {
            $apiKey = config('services.openweather.key');
            if (!$apiKey) {
                return $this->fallbackWeather($city);
            }

            // $lat = $this->coords[$city]['lat'] ?? -1.2921;
            // $lon = $this->coords[$city]['lon'] ?? 36.8219;
            // $tz  = $this->coords[$city]['tz']  ?? 'Africa/Nairobi';

            if (isset($this->coords[$city])) {
                    $lat = $this->coords[$city]['lat'];
                    $lon = $this->coords[$city]['lon'];
                    $tz  = $this->coords[$city]['tz'];
                } else {
                    // Geocode unknown city via OWM geo API
                    $geoRes = Http::timeout(4)->get('http://api.openweathermap.org/geo/1.0/direct', [
                        'q'     => $city,
                        'limit' => 1,
                        'appid' => $apiKey,
                    ]);
                    if ($geoRes->successful() && count($geoRes->json()) > 0) {
                        $geo = $geoRes->json()[0];
                        $lat = $geo['lat'];
                        $lon = $geo['lon'];
                        // Resolve timezone from coordinates via OWM (fallback to UTC offset lookup)
                        $tz = \Carbon\Carbon::now('UTC')->setTimezone(
                            timezone_name_from_abbr('', (int)($lon / 15) * 3600, false) ?: 'UTC'
                        )->getTimezone()->getName();
                    } else {
                        $lat = -1.2921;
                        $lon = 36.8219;
                        $tz  = 'Africa/Nairobi';
                    }
                }

            try {
                // ── 1. Current weather ─────────────────────────────────────
                $currentRes = Http::timeout(5)->get(
                    'https://api.openweathermap.org/data/4.0/onecall/current',
                    ['lat' => $lat, 'lon' => $lon, 'appid' => $apiKey, 'units' => 'metric']
                );

                if (!$currentRes->successful()) {
                    return $this->fallbackWeather($city);
                }

                $c       = $currentRes->json()['current'];
                // $now     = \Carbon\Carbon::now(new \DateTimeZone($tz));  
                $now     = \Carbon\Carbon::now('Africa/Nairobi');
                $sunrise = \Carbon\Carbon::createFromTimestamp($c['sunrise'])->setTimezone($tz);
                $sunset  = \Carbon\Carbon::createFromTimestamp($c['sunset'])->setTimezone($tz);

                // ── 2. 1-minute timeline (next 5 minutes precipitation) ────
                $minuteTrend = [];
                try {
                    $minRes = Http::timeout(5)->get(
                        'https://api.openweathermap.org/data/4.0/onecall/timeline/1min',
                        ['lat' => $lat, 'lon' => $lon, 'appid' => $apiKey, 'units' => 'metric']
                    );
                    if ($minRes->successful()) {
                        $minuteTrend = collect($minRes->json()['minutely'] ?? [])
                            ->take(5)
                            ->map(fn($m) => [
                                'time'          => \Carbon\Carbon::createFromTimestamp($m['dt'])->setTimezone($tz)->format('H:i'),
                                'precipitation' => round($m['precipitation'] ?? 0, 2),
                            ])
                            ->values()
                            ->toArray();
                    }
                } catch (\Exception $e) {
                    \Log::warning('Weather 1min timeline failed: ' . $e->getMessage());
                }

                // ── 3. Active weather alerts ───────────────────────────────
                $alerts = [];
                $alertIds = $currentRes->json()['alerts'] ?? [];
                foreach ($alertIds as $alert) {
                    try {
                        if (!empty($alert['id'])) {
                            $alertRes = Http::timeout(5)->get(
                                "https://api.openweathermap.org/data/4.0/onecall/alert/{$alert['id']}",
                                ['appid' => $apiKey]
                            );
                            if ($alertRes->successful()) {
                                $a = $alertRes->json();
                                $alerts[] = [
                                    'event'       => $a['event'] ?? $alert['event'] ?? 'Weather Alert',
                                    'source'      => $a['sender_name'] ?? $alert['sender_name'] ?? '',
                                    'description' => $a['description'] ?? $alert['description'] ?? '',
                                    'start'       => \Carbon\Carbon::createFromTimestamp($a['start'] ?? $alert['start'])->setTimezone($tz)->format('d M H:i'),
                                    'end'         => \Carbon\Carbon::createFromTimestamp($a['end'] ?? $alert['end'])->setTimezone($tz)->format('d M H:i'),
                                ];
                            }
                        } else {
                            // Alert has no separate ID endpoint — use inline data
                            $alerts[] = [
                                'event'       => $alert['event'] ?? 'Weather Alert',
                                'source'      => $alert['sender_name'] ?? '',
                                'description' => $alert['description'] ?? '',
                                'start'       => \Carbon\Carbon::createFromTimestamp($alert['start'])->setTimezone($tz)->format('d M H:i'),
                                'end'         => \Carbon\Carbon::createFromTimestamp($alert['end'])->setTimezone($tz)->format('d M H:i'),
                            ];
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Weather alert fetch failed: ' . $e->getMessage());
                    }
                }

                return [
                    // Core
                    'temperature'  => round($c['temp']),
                    'feels_like'   => round($c['feels_like']),
                    'condition'    => $c['weather'][0]['main'],
                    'description'  => ucfirst($c['weather'][0]['description']),
                    'icon'         => $c['weather'][0]['icon'],
                    'humidity'     => $c['humidity'],
                    'pressure'     => $c['pressure'] ?? null,
                    'dew_point'    => isset($c['dew_point']) ? round($c['dew_point']) : null,
                    'wind_speed'   => round($c['wind_speed']),
                    'wind_deg'     => $c['wind_deg'] ?? null,
                    'uvi'          => $c['uvi'] ?? null,
                    'cloud_cover'  => $c['clouds'] ?? null,
                    'visibility'   => isset($c['visibility']) ? round($c['visibility'] / 1000, 1) : null,
                    'city'         => $city,
                    // Time (accurate, timezone-aware)
                    'local_time'   => $now->format('g:i A'),
                    'local_date'   => $now->format('l, d M Y'),
                    'timezone'     => $tz,
                    'sunrise'      => $sunrise->format('H:i'),
                    'sunset'       => $sunset->format('H:i'),
                    // Extended
                    'minute_trend' => $minuteTrend,
                    'alerts'       => $alerts,
                ];

            } catch (\Exception $e) {
                \Log::warning('Weather API failed: ' . $e->getMessage());
                return $this->fallbackWeather($city);
            }
        });
    }

  private function fallbackWeather($city = 'Nairobi')
    {
        return [
            'temperature' => 25,
            'feels_like'  => 24,
            'condition'   => 'Clear',
            'description' => 'Clear sky',
            'icon'        => '01d',
            'humidity'    => 60,
            'wind_speed'  => 3,
            'uvi'         => null,
            'visibility'  => null,
            'city'        => $city,
        ];
    }
}
