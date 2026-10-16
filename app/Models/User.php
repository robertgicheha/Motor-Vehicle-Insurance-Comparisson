<?php

namespace App\Models;

use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * Passwords are issued by an admin and must be rotated every 30 days.
     * A dashboard reminder starts showing once the password is within
     * WARNING_DAYS of expiring.
     */
    public const PASSWORD_LIFETIME_DAYS = 30;

    public const PASSWORD_WARNING_DAYS = 7;

    protected $fillable = ['username', 'branch', 'password', 'password_changed_at', 'is_active', 'verified_at', 'profile_completed', 'avatar', 'last_online_at'];

    #[Hidden(['password', 'remember_token'])]

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'password' => 'hashed',
            // The branch is encrypted at rest with the app key, so a copied
            // database dump is unreadable without APP_KEY. See docs/security.md.
            'branch' => 'encrypted',
            'is_active' => 'boolean',
            'profile_completed' => 'boolean',
            'last_online_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Guarantee every account has a password-issued timestamp so the
        // 30-day expiry check never has to special-case a null value.
        static::creating(function (self $user) {
            if ($user->password && ! $user->password_changed_at) {
                $user->password_changed_at = now();
            }
        });
    }

    /**
     * Build the next available login username.
     *
     * Super-admin and admin accounts get a fixed, branch-independent username
     * ("superadmin" / "admin"). Only the plain "user" role is numbered per
     * branch, e.g. "Westlands" -> user1_westlands, user2_westlands, ... (one
     * login per branch member).
     */
    public static function generateUsername(string $branch, string $role = 'user'): string
    {
        if ($role === 'super-admin') {
            return static::nextAvailableUsername('superadmin');
        }

        if ($role === 'admin') {
            return static::nextAvailableUsername('admin');
        }

        $slug = Str::slug($branch, '_');
        if ($slug === '') {
            $slug = 'branch';
        }

        $number = 1;
        do {
            $candidate = "user{$number}_{$slug}";
            $number++;
        } while (static::withTrashed()->where('username', $candidate)->exists());

        return $candidate;
    }

    /**
     * Return the base username, appending a number only if it is already
     * taken (e.g. a second admin becomes admin2) so the unique index is
     * never hit. Admin/super-admin usernames never carry a branch suffix.
     */
    protected static function nextAvailableUsername(string $base): string
    {
        $candidate = $base;
        $number = 2;
        while (static::withTrashed()->where('username', $candidate)->exists()) {
            $candidate = $base . $number;
            $number++;
        }

        return $candidate;
    }

    public function passwordExpiresAt(): ?Carbon
    {
        return $this->password_changed_at?->copy()->addDays(self::PASSWORD_LIFETIME_DAYS);
    }

    /**
     * Deliberately returns a plain bool and never throws: a corrupt/missing
     * timestamp must not be able to take the login flow down. Callers decide
     * how to present the (string) message.
     */
    public function isPasswordExpired(): bool
    {
        try {
            $expiresAt = $this->passwordExpiresAt();

            return $expiresAt === null || now()->greaterThanOrEqualTo($expiresAt);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    public function daysUntilPasswordExpiry(): ?int
    {
        try {
            $expiresAt = $this->passwordExpiresAt();
            if ($expiresAt === null) {
                return 0;
            }

            return max(0, (int) now()->diffInDays($expiresAt, false));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function isPasswordExpiringSoon(): bool
    {
        $days = $this->daysUntilPasswordExpiry();

        return $days !== null && $days <= self::PASSWORD_WARNING_DAYS;
    }

    public function markPasswordChanged(): void
    {
        $this->forceFill(['password_changed_at' => now()])->save();
    }

    /**
     * A string the UI can show verbatim. Kept as a message (not an exception)
     * so the reminder flow degrades gracefully instead of erroring out.
     */
    public function passwordExpiryMessage(): ?string
    {
        if (! $this->isPasswordExpired()) {
            return null;
        }

        return 'Your password has expired. Please set a new password to continue.';
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isOnline()
    {
        return $this->last_online_at && $this->last_online_at->gt(now()->subMinutes(5));
    }

    /**
     * Accounts no longer store a personal name. The username (e.g.
     * "user1_westlands") is the only identifier shown and logged, so the
     * legacy `name` accessor resolves to it for every existing caller.
     */
    public function getNameAttribute(): ?string
    {
        return $this->username;
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->username;
    }
}
