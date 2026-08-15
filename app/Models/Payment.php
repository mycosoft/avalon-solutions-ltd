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
        'payee_name',
        'amount_paid',
        'daily_rate',
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

    public static function calculateBalance($patientId, $daysPaid): float
    {
        $patient = Patient::find($patientId);
        $totalDays = now()->diffInDays($patient->date_of_admission);
        $totalAmount = $totalDays * $patient->amount_to_pay;
        $paidAmount = self::where('patient_id', $patientId)->sum('amount_paid');
        return max(0, $totalAmount - $paidAmount);
    }
}
