<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'status' => 'boolean',
    ];

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'caregiver_patient')
            ->withPivot('assignment_date', 'is_active')
            ->wherePivot('is_active', true);
    }

    public function allPatients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'caregiver_patient')
            ->withPivot('assignment_date', 'is_active');
    }
}
