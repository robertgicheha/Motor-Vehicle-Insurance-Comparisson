<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Superadmin-only manager for the mail server credentials.
 *
 * The values shown and saved here are the environment variables in the
 * server's .env file (MAIL_*), never application code and never the
 * database. The .env file can be encrypted at rest with Laravel's built-in
 * environment encryption (php artisan env:encrypt), which this page exposes
 * via the "Encrypt environment file" action.
 *
 * Two delivery providers are supported:
 *   - "default": the standard MAIL_* transport (Postmark, SES, etc.)
 *   - "lolc":    LOLC's own SMTP server, configured through the separate
 *                LOLC_MAIL_* credentials, so mail is sent from their server.
 */
class EmailConfigController extends Controller
{
    /**
     * The default-provider environment keys, mapped to their form field names.
     */
    protected array $keys = [
        'mailer' => 'MAIL_MAILER',
        'host' => 'MAIL_HOST',
        'port' => 'MAIL_PORT',
        'username' => 'MAIL_USERNAME',
        'password' => 'MAIL_PASSWORD',
        'encryption' => 'MAIL_ENCRYPTION',
        'from_address' => 'MAIL_FROM_ADDRESS',
        'from_name' => 'MAIL_FROM_NAME',
    ];

    /**
     * The Custom SMTP (LOLC) environment keys, mapped to their form field names.
     */
    protected array $lolcKeys = [
        'lolc_host' => 'LOLC_MAIL_HOST',
        'lolc_port' => 'LOLC_MAIL_PORT',
        'lolc_username' => 'LOLC_MAIL_USERNAME',
        'lolc_password' => 'LOLC_MAIL_PASSWORD',
        'lolc_encryption' => 'LOLC_MAIL_ENCRYPTION',
        'lolc_from_address' => 'LOLC_MAIL_FROM_ADDRESS',
        'lolc_from_name' => 'LOLC_MAIL_FROM_NAME',
    ];

    public function index()
    {
        $envPath = base_path('.env');
        $encryptedPath = base_path('.env.encrypted');

        // Read through config (never env() directly): once the config is
        // cached, env() returns null outside config files.
        $config = [
            'provider' => config('mail.provider', 'default'),
            'mailer' => config('mail.base_default', 'log'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'encryption' => config('mail.mailers.smtp.encryption') ?: null,
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        $lolc = [
            'host' => config('mail.mailers.lolc.host'),
            'port' => config('mail.mailers.lolc.port'),
            'username' => config('mail.mailers.lolc.username'),
            'password' => config('mail.mailers.lolc.password'),
            'encryption' => config('mail.mailers.lolc.encryption') ?: null,
            'from_address' => config('mail.mailers.lolc.from.address'),
            'from_name' => config('mail.mailers.lolc.from.name'),
        ];

        $envEncryptedAt = file_exists($encryptedPath) ? date('d M Y, H:i', filemtime($encryptedPath)) : null;
        $envWritable = is_writable($envPath);

        return view('admin.email-config.index', compact('config', 'lolc', 'envWritable', 'envEncryptedAt'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:default,lolc',
            'mailer' => 'required|string|max:50',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|in:tls,ssl,',
            'from_address' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            // LOLC custom SMTP: host + port become mandatory once it is the
            // active provider, otherwise the portal would silently fail to send.
            'lolc_host' => 'required_if:provider,lolc|nullable|string|max:255',
            'lolc_port' => 'required_if:provider,lolc|nullable|integer|min:1|max:65535',
            'lolc_username' => 'nullable|string|max:255',
            'lolc_password' => 'nullable|string|max:255',
            'lolc_encryption' => 'nullable|in:tls,ssl,',
            'lolc_from_address' => 'nullable|email|max:255',
            'lolc_from_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except(['password', 'lolc_password']));
        }

        $values = $validator->validated();

        // The password fields are write-only: an empty submission means "leave
        // the existing secret untouched" rather than "clear the password".
        $password = $request->filled('password') ? $values['password'] : null;
        $lolcPassword = $request->filled('lolc_password') ? $values['lolc_password'] : null;

        $envPath = base_path('.env');

        if (!is_writable($envPath)) {
            return back()->with('error', 'The .env file is not writable on this server. Update the credentials manually.');
        }

        try {
            $this->setEnvValue('MAIL_PROVIDER', $values['provider']);

            $this->setEnvValue('MAIL_MAILER', $values['mailer']);
            $this->setEnvValue('MAIL_HOST', $values['host'] ?? '');
            $this->setEnvValue('MAIL_PORT', $values['port'] ?? '');
            $this->setEnvValue('MAIL_USERNAME', $values['username'] ?? '');
            if ($password !== null) {
                $this->setEnvValue('MAIL_PASSWORD', $password);
            }
            $this->setEnvValue('MAIL_ENCRYPTION', $values['encryption'] ?? '');
            $this->setEnvValue('MAIL_FROM_ADDRESS', $values['from_address'] ?? '');
            $this->setEnvValue('MAIL_FROM_NAME', $values['from_name'] ?? '');

            $this->setEnvValue('LOLC_MAIL_HOST', $values['lolc_host'] ?? '');
            $this->setEnvValue('LOLC_MAIL_PORT', $values['lolc_port'] ?? '');
            $this->setEnvValue('LOLC_MAIL_USERNAME', $values['lolc_username'] ?? '');
            if ($lolcPassword !== null) {
                $this->setEnvValue('LOLC_MAIL_PASSWORD', $lolcPassword);
            }
            $this->setEnvValue('LOLC_MAIL_ENCRYPTION', $values['lolc_encryption'] ?? '');
            $this->setEnvValue('LOLC_MAIL_FROM_ADDRESS', $values['lolc_from_address'] ?? '');
            $this->setEnvValue('LOLC_MAIL_FROM_NAME', $values['lolc_from_name'] ?? '');
        } catch (\Throwable $e) {
            Log::error('Mail settings could not be written to .env: ' . $e->getMessage());

            return back()->with('error', 'Could not write to the .env file. Please check file permissions.');
        }

        Artisan::call('config:clear');

        $providerLabel = $values['provider'] === 'lolc' ? 'Custom SMTP (LOLC)' : 'Default';
        AuditService::activity('mail_settings_updated', 'Mail provider (' . $providerLabel . ') updated in .env by ' . auth()->user()->username);

        return back()->with('success', 'Mail settings saved to .env and configuration cache cleared.');
    }

    public function encrypt()
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return back()->with('error', 'No .env file found to encrypt.');
        }

        $key = config('app.key');

        if (!$key) {
            return back()->with('error', 'APP_KEY is not set, so the environment cannot be encrypted.');
        }

        try {
            // Pass the application key explicitly: without --key, a
            // non-interactive env:encrypt call silently generates a throwaway
            // random key that is never persisted, making the encrypted file
            // impossible to decrypt later.
            Artisan::call('env:encrypt', ['--key' => $key, '--force' => true]);
        } catch (\Throwable $e) {
            Log::error('env:encrypt failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to encrypt the environment file: ' . $e->getMessage());
        }

        if (!file_exists(base_path('.env.encrypted'))) {
            return back()->with('error', 'Encryption did not produce a .env.encrypted file. Check that APP_KEY is set.');
        }

        AuditService::activity('env_encrypted', 'Environment file encrypted by ' . auth()->user()->username);

        return back()->with('success', 'Environment file encrypted to .env.encrypted. Keep that file safe and out of version control.');
    }

    /**
     * Write (or replace) a single key in the .env file, preserving the rest of
     * the file exactly. Values containing spaces or special characters are
     * wrapped in double quotes, as Laravel's dotenv parser expects.
     */
    protected function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException('Unable to read .env');
        }

        $value = (string) $value;

        if ($value === '') {
            $line = $key . '=';
        } elseif (preg_match('/[\s#"\'=>]/', $value)) {
            $escaped = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
            $line = $key . '="' . $escaped . '"';
        } else {
            $line = $key . '=' . $value;
        }

        $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

        if (preg_match($pattern, $contents)) {
            $contents = preg_replace($pattern, str_replace(['\\', '$'], ['\\\\', '\\$'], $line), $contents, 1);
        } else {
            $contents = rtrim($contents, "\r\n") . PHP_EOL . $line . PHP_EOL;
        }

        if (file_put_contents($path, $contents) === false) {
            throw new \RuntimeException('Unable to write .env');
        }
    }
}
