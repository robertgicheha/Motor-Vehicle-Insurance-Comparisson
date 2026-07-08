<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // General
        Setting::setValue('app_name', 'Motor Vehicle Insurance Dashboard', 'general');
        Setting::setValue('app_url', url('/'), 'general');
        Setting::setValue('app_version', '1.0.0', 'general');
        Setting::setValue('min_valuation', '800000', 'general');
        Setting::setValue('max_valuation', '1300000', 'general');
        Setting::setValue('currency', 'KES', 'general');
        Setting::setValue('currency_symbol', 'KES', 'general');
        Setting::setValue('timezone', 'Africa/Nairobi', 'general');
        Setting::setValue('date_format', 'd M Y', 'general');
        Setting::setValue('weather_city', 'Nairobi', 'general');
        Setting::setValue('weather_country', 'KE', 'general');
        Setting::setValue('report_email', 'robertgicheha3@gmail.com', 'general');
        Setting::setValue('admin_email', 'robertgicheha3@gmail.com', 'general');
        Setting::setValue('company_name', 'MIC Global Risks', 'general');
        Setting::setValue('company_address', 'Nairobi, Kenya', 'general');
        Setting::setValue('company_phone', '+254 700 000 000', 'general');
        Setting::setValue('pagination_per_page', '20', 'general');

        // Email / Mail
        Setting::setValue('mail_mailer', 'smtp', 'email');
        Setting::setValue('mail_host', 'smtp.mailtrap.io', 'email');
        Setting::setValue('mail_port', '587', 'email');
        Setting::setValue('mail_username', '', 'email');
        Setting::setValue('mail_password', '', 'email');
        Setting::setValue('mail_encryption', 'tls', 'email');
        Setting::setValue('mail_from_address', 'robertgicheha3@gmail.com', 'email');
        Setting::setValue('mail_from_name', 'MIC Insurance Portal', 'email');

        // Quotation defaults
        Setting::setValue('default_cover_type', 'comprehensive', 'quotations');
        Setting::setValue('default_excess_own_damage', '5000', 'quotations');
        Setting::setValue('default_excess_theft', '5000', 'quotations');
        Setting::setValue('quotation_validity_days', '30', 'quotations');
        Setting::setValue('enable_pvt', 'true', 'quotations');
        Setting::setValue('pvt_rate', '0.45', 'quotations');
        Setting::setValue('max_quotation_items', '10', 'quotations');

        // API
        Setting::setValue('sticker_api_key', \Illuminate\Support\Str::random(32), 'api');
        Setting::setValue('api_rate_limit', '60', 'api');
        Setting::setValue('enable_public_api', 'true', 'api');

        // Notifications
        Setting::setValue('notify_on_new_quotation', 'true', 'notifications');
        Setting::setValue('notify_on_new_user', 'true', 'notifications');
        Setting::setValue('notify_on_new_enquiry', 'true', 'notifications');
        Setting::setValue('notify_admin_email', 'robertgicheha3@gmail.com', 'notifications');

        // Appearance
        Setting::setValue('primary_color', '#1756c8', 'appearance');
        Setting::setValue('sidebar_collapsed', 'false', 'appearance');
        Setting::setValue('dark_mode_default', 'false', 'appearance');
        Setting::setValue('logo_path', 'images/MIC-Global-Risks_logo_no_background.png', 'appearance');
        Setting::setValue('favicon_path', '', 'appearance');

        // Security
        Setting::setValue('session_lifetime', '120', 'security');
        Setting::setValue('password_min_length', '8', 'security');
        Setting::setValue('max_login_attempts', '5', 'security');
        Setting::setValue('lockout_duration', '15', 'security');
        Setting::setValue('require_email_verification', 'true', 'security');
        Setting::setValue('two_factor_auth', 'false', 'security');

        // Integration
        Setting::setValue('openweather_api_key', '', 'integrations');
        Setting::setValue('google_client_id', '', 'integrations');
        Setting::setValue('google_client_secret', '', 'integrations');
        Setting::setValue('enable_social_login', 'true', 'integrations');
    }
}
