<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;

class EmailSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'user_credentials' => [
                'subject' => 'Your Account Credentials - Motor Vehicle Insurance Portal',
                'body' => '<h2>Hello {{ user_name }},</h2>'
                    . '<p>Welcome to the <strong>Motor Vehicle Insurance Portal</strong>. Your account has been created successfully. Please find your login credentials below:</p>'
                    . '<table>'
                    . '<tr><td><strong>Email</strong></td><td>{{ user_email }}</td></tr>'
                    . '<tr><td><strong>Password</strong></td><td><code>{{ password }}</code></td></tr>'
                    . '</table>'
                    . '<p style="text-align:center;margin-top:16px"><a href="{{ login_url }}" class="btn">Login to Portal</a></p>'
                    . '<p>For security reasons, please navigate to the Profile section after logging in to change your password.</p>',
                'type' => 'user',
            ],
            'quotation' => [
                'subject' => 'A Quotation {{ quotation_number }} has been created successfully',
                'body' => '<h2>Dear {{ customer_name }},</h2>'
                    . '<p>A quotation with the quotation no <strong>{{ quotation_number }}</strong> has been generated successfully.</p>'
                    . '<p>Thank you for choosing <strong>{{ company }}</strong>.</p>'
                    . '<div class="premium-box"><div class="label">Total Premium</div><div class="amount">KES {{ total_premium }}</div></div>'
                    . '<table>'
                    . '<tr><td>Quotation Number</td><td><strong>{{ quotation_number }}</strong></td></tr>'
                    . '<tr><td>Insurance Company</td><td>{{ company }}</td></tr>'
                    . '<tr><td>Vehicle</td><td>{{ vehicle }}</td></tr>'
                    . '<tr><td>Vehicle Valuation</td><td>KES {{ valuation }}</td></tr>'
                    . '<tr><td>Basic Premium</td><td>KES {{ premium }}</td></tr>'
                    . '</table>'
                    . '<p style="text-align:center;margin-top:16px"><a href="{{ view_url }}" class="btn">View Quotation Online</a></p>'
                    . '<p style="font-size:11px;color:#94a3b8">This quotation is valid for 15 days from the date of issue. Subject to standard policy terms and conditions.</p>',
                'type' => 'user',
            ],
            'sticker' => [
                'subject' => 'Insurance Certificate - {{ sticker_number }}',
                'body' => '<h2>Dear {{ customer_name }},</h2>'
                    . '<p>Your motor vehicle insurance certificate with <strong>{{ company }}</strong> has been issued successfully.</p>'
                    . '<div class="premium-box"><div class="label">Total Premium</div><div class="amount">KES {{ premium }}</div></div>'
                    . '<table>'
                    . '<tr><td>Certificate Number</td><td><strong>{{ sticker_number }}</strong></td></tr>'
                    . '<tr><td>Insurance Company</td><td>{{ company }}</td></tr>'
                    . '<tr><td>Vehicle</td><td>{{ vehicle }}</td></tr>'
                    . '<tr><td>Vehicle Valuation</td><td>KES {{ valuation }}</td></tr>'
                    . '</table>'
                    . '<p style="text-align:center;margin-top:16px"><a href="{{ view_url }}" class="btn">View Certificate Online</a></p>',
                'type' => 'user',
            ],
            'welcome' => [
                'subject' => 'Welcome to Motor Vehicle Insurance Portal',
                'body' => '<h2>Hello {{ user_name }},</h2>'
                    . '<p>Thank you for creating an account with us. You can now:</p>'
                    . '<ul>'
                    . '<li>Generate insurance quotations</li>'
                    . '<li>Compare rates from multiple insurers</li>'
                    . '<li>Download certificates and stickers</li>'
                    . '<li>Track your quotation status</li>'
                    . '</ul>'
                    . '<p style="text-align:center;margin-top:16px"><a href="{{ dashboard_url }}" class="btn">Go to Dashboard</a></p>'
                    . '<p>If you signed up with Google, please complete your profile to unlock all features.</p>'
                    . '<p style="text-align:center"><a href="{{ profile_url }}" class="btn" style="background:#0f172a">Complete Profile</a></p>',
                'type' => 'user',
            ],
            'account_pending_verification' => [
                'subject' => 'Your Account Has Been Created — Pending Verification',
                'body' => '<h2>Hello {{ user_name }},</h2>'
                    . '<p>Thank you for creating an account with the <strong>Motor Vehicle Insurance Portal</strong>. Your account has been created successfully, but it is currently <strong>pending verification</strong> by our team.</p>'
                    . '<p>You will receive another email as soon as your account has been verified — after that you\'ll be able to log in, generate quotations, and request stickers.</p>',
                'type' => 'user',
            ],
            'account_verified' => [
                'subject' => 'Your Account Has Been Verified',
                'body' => '<h2>Hello {{ user_name }},</h2>'
                    . '<p>Great news — your account has been verified successfully. You can now log in to the dashboard, generate quotations, and request stickers.</p>'
                    . '<p style="text-align:center;margin-top:16px"><a href="{{ login_url }}" class="btn">Login to Portal</a></p>',
                'type' => 'user',
            ],
            'pending_email_verification' => [
                'subject' => 'Verify Your New Email Address',
                'body' => '<h2>Hello {{ user_name }},</h2>'
                    . '<p>You requested to change your email address to <strong>{{ user_email }}</strong>. Please verify your new email address by clicking the button below.</p>'
                    . '<p style="text-align:center;margin-top:16px"><a href="{{ verify_url }}" class="btn">Verify Email Address</a></p>'
                    . '<p style="font-size:11px;color:#94a3b8">If you did not request this change, you can safely ignore this email.</p>',
                'type' => 'user',
            ],
        ];

        foreach ($defaults as $key => $data) {
            EmailSetting::updateOrCreate(
                ['key' => $key],
                $data + [
                    'letterhead_company' => 'MIC Global Risks',
                    'letterhead_color' => '#004f8c',
                    'letterhead_logo' => null,
                    'letterhead_address' => 'MIC Global Risks (Kenya) Limited, P.O. Box 10000 - 00100 Nairobi, Kenya',
                    'letterhead_website' => 'www.micglobalrisks.com',
                ]
            );
        }
    }
}
