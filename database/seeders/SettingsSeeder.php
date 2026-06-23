<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setValue('app_name', 'Motor Vehicle Insurance Dashboard', 'general');
        Setting::setValue('min_valuation', '800000', 'general');
        Setting::setValue('max_valuation', '1300000', 'general');
        Setting::setValue('currency', 'KES', 'general');
        Setting::setValue('weather_city', 'Nairobi', 'general');
        Setting::setValue('weather_country', 'KE', 'general');
        Setting::setValue('report_email', 'admin@insurance.com', 'general');
        Setting::setValue('sticker_api_key', \Illuminate\Support\Str::random(32), 'api');
    }
}
