<?php

namespace App\Models;

use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'name', 'email', 'password', 'mobile', 'gender', 'is_active', 'verified_at', 'profile_completed', 'google_id', 'avatar', 'last_online_at', 'pending_email', 'email_verify_token'];

    #[Hidden(['password', 'remember_token'])]

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'profile_completed' => 'boolean',
            'last_online_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * A self-registered account awaiting admin approval — distinct from a
     * normal account an admin later deactivated (which keeps its original
     * verified_at), so the Pending Users queue only ever shows genuinely new
     * signups.
     */
    public function isPendingVerification(): bool
    {
        return !$this->is_active && is_null($this->verified_at);
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

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->name;
    }
}
