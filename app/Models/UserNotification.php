<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function notifyAdmins(string $title, string $message, string $type = 'info')
    {
        $admins = User::whereIn('role_type', ['superadmin', 'admin'])->get();
        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
            ]);
        }
    }

    public static function notifyUser(int $userId, string $title, string $message, string $type = 'info')
    {
        self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }

    public static function notifyAccountants(string $title, string $message, string $type = 'info')
    {
        $users = User::whereIn('role_type', ['superadmin', 'admin', 'accountant'])->get();
        foreach ($users as $user) {
            self::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
            ]);
        }
    }
}
