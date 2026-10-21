<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usage: ActivityLog::record('post.created', 'Created post "Hello"', $post);
     */
    public static function record(string $action, string $description, ?Model $subject = null, ?int $userId = null): void
    {
        try {
            static::create([
                'user_id'      => $userId ?? Auth::id(),
                'action'       => $action,
                'description'  => $description,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id'   => $subject?->getKey(),
                'ip_address'   => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // logging must never break the app
            report($e);
        }
    }
}