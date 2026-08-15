<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_type',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_type === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role_type, ['superadmin', 'admin']);
    }

    public function isAccountant(): bool
    {
        return in_array($this->role_type, ['superadmin', 'admin', 'accountant']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role_type) {
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'accountant' => 'Accountant',
            default => ucfirst($this->role_type ?? 'Unknown'),
        };
    }

    public function adminlte_image()
    {
        return asset('images/logo.png');
    }

    public function adminlte_profile_url()
    {
        return route('profile.edit');
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->userNotifications()->whereNull('read_at');
    }
}
