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

    /**
     * All categories used in expenses — predefined set merged with
     * distinct categories actually entered into the database.
     * Useful for dynamic filters and form suggestions.
     */
    public static function allCategories(): array
    {
        $predefined = array_values(self::categories());

        $fromDb = self::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return array_values(array_unique(array_merge($predefined, $fromDb)));
    }
}
