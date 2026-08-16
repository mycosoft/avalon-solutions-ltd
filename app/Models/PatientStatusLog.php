<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'status',
        'effective_date',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
