<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Set once per request the first time an explicit audit/activity entry is
     * written, so the LogActivity middleware's generic catch-all can skip
     * requests that already produced a specific, human-readable log entry —
     * this is what prevents the same request from being logged twice.
     */
    protected static bool $loggedExplicitly = false;

    /**
     * The public IP address of the current request. Because the app runs
     * behind a managed reverse proxy (Render) and trustProxies is enabled in
     * bootstrap/app.php, Request::ip() resolves the real client IP from the
     * X-Forwarded-For header rather than the proxy's private address.
     */
    public static function clientIp(): ?string
    {
        return Request::ip();
    }

    /**
     * Structured audit-trail entry (what changed: model, old/new values).
     */
    public static function log($action, $model = null, $modelId = null, $oldValues = null, $newValues = null, $userId = null)
    {
        static::$loggedExplicitly = true;

        [$userId, $userName] = self::resolveActor($userId);

        return AuditLog::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => self::scrub($oldValues, $model),
            'new_values' => self::scrub($newValues, $model),
            'ip_address' => self::clientIp(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Human-readable event log (what happened, when, by whom, from where).
     */
    public static function activity($action, $description = null, $level = 'info', $userId = null)
    {
        static::$loggedExplicitly = true;

        [$userId, $userName] = self::resolveActor($userId);

        return ActivityLog::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'level' => $level,
            'action' => $action,
            'description' => $description,
            'ip_address' => self::clientIp(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function hasLoggedExplicitly(): bool
    {
        return static::$loggedExplicitly;
    }

    /**
     * Resolve the acting user's id and a snapshot of their username, so the
     * log still records who did something even after the account is deleted.
     * The username is a non-personal identifier (e.g. user1_westlands).
     */
    protected static function resolveActor($userId): array
    {
        $actor = auth()->user();

        if ($userId !== null) {
            if ($actor && (int) $actor->id === (int) $userId) {
                return [$actor->id, $actor->username];
            }

            $username = User::withTrashed()->find($userId)?->username;

            return [$userId, $username];
        }

        return [$actor?->id, $actor?->username];
    }

    /**
     * Personal-data keys that must never be persisted in audit value
     * snapshots, even when a whole model is passed via toArray(). Chosen by
     * the audited model so a company's "name" is untouched while a user's
     * personal name is stripped.
     */
    protected const PERSONAL_KEYS_BY_MODEL = [
        'User' => ['first_name', 'last_name', 'name', 'full_name', 'gender', 'mobile', 'email'],
        'Quotation' => ['salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'email'],
        'Sticker' => ['customer_name', 'customer_email', 'customer_phone', 'email_body', 'email_from_name'],
        'Claim' => ['customer_name', 'customer_email', 'customer_phone'],
        'Enquiry' => ['name', 'email'],
    ];

    protected const PERSONAL_KEYS_FALLBACK = [
        'salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'mobile', 'email',
        'customer_name', 'customer_email', 'customer_phone',
        'customer_salutation', 'customer_first_name', 'customer_last_name',
        'email_body', 'email_from_name', 'full_name',
    ];

    /**
     * Remove personal-data keys from an array of attributes before it is
     * written to the audit trail.
     */
    protected static function scrub($values, $model): array
    {
        if (! is_array($values) || $values === []) {
            return is_array($values) ? $values : [];
        }

        $keys = self::PERSONAL_KEYS_BY_MODEL[$model] ?? self::PERSONAL_KEYS_FALLBACK;

        foreach ($keys as $key) {
            unset($values[$key]);
        }

        return $values;
    }
}
