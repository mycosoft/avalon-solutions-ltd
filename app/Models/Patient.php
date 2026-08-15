<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'gender',
        'relative_name',
        'ward',
        'amount_to_pay',
        'date_of_admission',
        'patient_status',
        'date_of_discharge',
        'date_of_transfer',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_phone',
        'next_of_kin_address',
        'transfer_notes',
        'discharge_notes',
        'is_active',
    ];

    protected $casts = [
        'date_of_admission' => 'date',
        'date_of_discharge' => 'date',
        'date_of_transfer' => 'date',
        'amount_to_pay' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function caregivers(): BelongsToMany
    {
        return $this->belongsToMany(Caregiver::class, 'caregiver_patient')
            ->withPivot('assignment_date', 'is_active')
            ->wherePivot('is_active', true);
    }

    public function allCaregivers(): BelongsToMany
    {
        return $this->belongsToMany(Caregiver::class, 'caregiver_patient')
            ->withPivot('assignment_date', 'is_active');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->patient_status) {
            'on_ward' => 'On Ward',
            'transferred' => 'Transferred',
            'discharged' => 'Discharged',
            default => ucfirst($this->patient_status ?? 'Unknown'),
        };
    }
}
