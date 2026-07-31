<?php

namespace App\Models;

use App\Enums\StoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryModeration extends Model
{
    protected $fillable = [
        'story_id',
        'moderator_id',
        'action',
        'reason',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    protected function casts(): array
    {
        return [
            'action' => StoryStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
