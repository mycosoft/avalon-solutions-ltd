<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caregiver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'photo',
        'name',
        'address',
        'phone',
        'nin',
        'date_of_birth',
        'gender',
        'level_of_education',
        'monthly_rate',
        'payment_plan',
        'date_of_entry',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_phone',
        'next_of_kin_address',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_entry' => 'date',
        'monthly_rate' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Returns the pay rate per period (per day for daily, per month for monthly).
     */
    public function getRatePerPeriodAttribute(): float
    {
        return (float) $this->monthly_rate;
    }

    /**
     * Returns the rate per day, regardless of payment plan.
     */
    public function getDailyRateAttribute(): float
    {
        if ($this->payment_plan === 'monthly') {
            return round(((float) $this->monthly_rate) / 30, 2);
        }
        return (float) $this->monthly_rate;
    }

    /**
     * Suggested payment amount for a given period (days).
     */
    public function suggestedPayment(int $days = 1): float
    {
        if ($this->payment_plan === 'monthly') {
            return (float) $this->monthly_rate;
        }
        return round(((float) $this->monthly_rate) * $days, 2);
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'caregiver_patient')
            ->withPivot('assignment_date', 'is_active')
            ->wherePivot('is_active', true);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('payee_for', 'caregiver');
    }

    public function allPatients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'caregiver_patient')
            ->withPivot('assignment_date', 'is_active');
    }
}
