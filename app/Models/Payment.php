<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'payee_for',
        'caregiver_id',
        'payee_name',
        'amount_paid',
        'daily_rate',
        'monthly_rate',
        'days_paid',
        'payment_date',
        'period_start',
        'period_end',
        'payment_method',
        'payment_type',
        'balance',
        'notes',
        'recorded_by',
        'status',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'balance' => 'decimal:2',
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'status' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(Caregiver::class);
    }

    public static function calculateBalance($patientId, $daysPaid): float
    {
        $patient = Patient::find($patientId);
        $totalDays = now()->diffInDays($patient->date_of_admission);
        $totalAmount = $totalDays * $patient->amount_to_pay;
        $paidAmount = self::where('patient_id', $patientId)->sum('amount_paid');
        return max(0, $totalAmount - $paidAmount);
    }

    /**
     * Scope: patient payments only.
     */
    public function scopeForPatients($query)
    {
        return $query->where('payee_for', 'patient')->orWhereNull('payee_for');
    }

    /**
     * Scope: caregiver payments only.
     */
    public function scopeForCaregivers($query)
    {
        return $query->where('payee_for', 'caregiver');
    }

    /**
     * Receipt number — formatted like PAY-20260816-0042
     */
    public function getReceiptNumberAttribute(): string
    {
        return 'PAY-' . $this->created_at->format('Ymd') . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * The payee (either patient or caregiver depending on payee_for).
     */
    public function getPayeeAttribute()
    {
        return $this->payee_for === 'caregiver' ? $this->caregiver : $this->patient;
    }

    /**
     * Cumulative amount paid by this patient up to and including this payment.
     */
    public function getCumulativePaidAttribute(): float
    {
        if (! $this->patient_id) {
            return (float) $this->amount_paid;
        }

        return (float) Payment::where('patient_id', $this->patient_id)
            ->where(function ($q) {
                $q->where('payee_for', 'patient')->orWhereNull('payee_for');
            })
            ->where(function ($q) {
                $q->where('payment_date', '<', $this->payment_date)
                  ->orWhere(function ($q2) {
                      // Same date — order by id so earlier receipts count first.
                      $q2->where('payment_date', '=', $this->payment_date)
                         ->where('id', '<=', $this->id);
                  });
            })
            ->sum('amount_paid');
    }

    /**
     * Remaining balance after this payment was applied.
     *
     * Uses the patient's CURRENT total due minus the cumulative
     * payments up to (and including) this one. This stays accurate
     * even as more days accrue without further payment.
     */
    public function getRunningBalanceAttribute(): float
    {
        if (! $this->patient) {
            return (float) $this->balance;
        }

        $totalDue       = $this->patient->total_due;
        $cumulativePaid = $this->cumulative_paid;

        return max(0, round($totalDue - $cumulativePaid, 2));
    }
}
