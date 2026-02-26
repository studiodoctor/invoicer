<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'type',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function log(
        Model $subject,
        string $type,
        string $description,
        array $properties = [],
        ?int $userId = null
    ): self {
        // Try to get user_id from auth, then from subject, then use provided userId
        $resolvedUserId = $userId ?? auth()->id();
        
        // If still null and subject has user_id, use that
        if ($resolvedUserId === null && isset($subject->user_id)) {
            $resolvedUserId = $subject->user_id;
        }

        return static::create([
            'user_id' => $resolvedUserId,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}