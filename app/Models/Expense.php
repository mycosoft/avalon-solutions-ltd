<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'expense_date',
        'category',
        'recorded_by',
        'notes',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'status' => 'boolean',
    ];

    public static function categories()
    {
        return [
            'general' => 'General',
            'salaries' => 'Salaries',
            'supplies' => 'Supplies',
            'utilities' => 'Utilities',
            'maintenance' => 'Maintenance',
            'transport' => 'Transport',
            'food' => 'Food',
            'medical' => 'Medical',
            'other' => 'Other',
        ];
    }
}
