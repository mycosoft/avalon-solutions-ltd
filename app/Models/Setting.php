<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Retrieve a setting value by key with an optional default.
     */
    public static function get(string $key, $default = null)
    {
        $row = self::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /**
     * Set / update a setting value.
     */
    public static function set(string $key, $value, string $group = 'general'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'group' => $group]
        );
    }

    /**
     * Get all settings as an associative array grouped by `group`.
     */
    public static function grouped(): array
    {
        return self::all()->groupBy('group')->mapWithKeys(function ($items, $group) {
            return [$group => $items->pluck('value', 'key')->toArray()];
        })->toArray();
    }
}
