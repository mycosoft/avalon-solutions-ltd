<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_id',
        'patient_id',
        'date',
        'ward',
        'days_under_care',
        'admin_observation',
        'complaint_reported',
        'complaint_assignment',
        'follow_up',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'boolean',
    ];

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(Caregiver::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
