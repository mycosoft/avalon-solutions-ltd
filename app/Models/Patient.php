<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PatientStatusLog::class)->orderBy('effective_date')->orderBy('id');
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

    /**
     * The reference end-date for billing.
     * Discharged patients stop accruing on their discharge date;
     * others continue accruing up to today.
     */
    public function getBillingEndDate(): \Carbon\Carbon
    {
        if ($this->patient_status === 'discharged' && $this->date_of_discharge) {
            return $this->date_of_discharge->copy()->startOfDay();
        }
        return now()->startOfDay();
    }

    /**
     * Number of days the patient has been admitted (inclusive of admission day).
     *
     * Counting rules:
     *   - "on_ward" periods count
     *   - "transferred" periods pause counting
     *   - "discharged" stops counting
     *
     * When status logs exist, the count is computed by summing every
     * on_ward interval between admission and the current status.
     * When no logs exist yet, falls back to a simple date-based estimate.
     */
    public function getDaysAdmittedAttribute(): int
    {
        if (! $this->date_of_admission) {
            return 0;
        }

        $admissionDate = $this->date_of_admission->copy()->startOfDay();
        $today         = now()->startOfDay();

        $logs = $this->statusLogs()->get();

        // Fallback when there is no history to consult.
        if ($logs->isEmpty()) {
            return $this->computeSimpleDaysAdmitted($admissionDate, $today);
        }

        $totalDays   = 0;
        $wardStart   = $admissionDate;

        foreach ($logs as $log) {
            $logDate = $log->effective_date->copy()->startOfDay();

            if (in_array($log->status, ['transferred', 'discharged'], true)) {
                // Close the current on-ward interval [wardStart, logDate].
                if ($wardStart && $logDate->gte($wardStart)) {
                    $totalDays += $wardStart->diffInDays($logDate) + 1;
                }
                $wardStart = null;
            } elseif ($log->status === 'on_ward') {
                // Re-open counting from this date.
                $wardStart = $logDate;
            }
        }

        // If currently on ward, close the open interval at today.
        if ($wardStart !== null && $this->patient_status === 'on_ward') {
            $totalDays += $wardStart->diffInDays($today) + 1;
        }

        return max(0, (int) $totalDays);
    }

    /**
     * Fallback when no status logs exist yet for a patient.
     * Uses the existing single-status-date fields.
     */
    protected function computeSimpleDaysAdmitted(\Carbon\Carbon $admissionDate, \Carbon\Carbon $today): int
    {
        $end = match ($this->patient_status) {
            'transferred' => $this->date_of_transfer
                ? $this->date_of_transfer->copy()->startOfDay()
                : $today,
            'discharged'  => $this->date_of_discharge
                ? $this->date_of_discharge->copy()->startOfDay()
                : $today,
            default       => $today,
        };

        return max(0, (int) $admissionDate->diffInDays($end) + 1);
    }

    /**
     * Cumulative amount that should have been paid since admission.
     */
    public function getTotalDueAttribute(): float
    {
        return round($this->days_admitted * (float) $this->amount_to_pay, 2);
    }

    /**
     * Sum of all payments recorded for this patient (patient-payments only).
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount_paid');
    }

    /**
     * Outstanding balance (Total Due - Total Paid), never negative.
     */
    public function getBalanceAttribute(): float
    {
        return max(0, round($this->total_due - $this->total_paid, 2));
    }

    /**
     * Days that have NOT yet been paid (days admitted - effective paid days).
     */
    public function getDaysOwedAttribute(): int
    {
        $dailyRate = (float) $this->amount_to_pay;
        if ($dailyRate <= 0) {
            return 0;
        }
        $paidDays = floor($this->total_paid / $dailyRate);
        return max(0, $this->days_admitted - (int) $paidDays);
    }
}
