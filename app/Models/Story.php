<?php

namespace App\Models;

use App\Enums\StoryStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Story extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'rejected_reason',
        'moderated_by',
        'published_at',
        'image_path'
    ];

    /*
     * Получение пользователя истории
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
     * Получение меток истории
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function moderations(): HasMany
    {
        return $this->hasMany(StoryModeration::class);
    }

    #[Scope]
    public function filter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, function (Builder $query, string $status): void {
                $statusEnum = StoryStatus::from($status);
                $query->where('status', $statusEnum->value);
            })
            ->when($filters['user_id'] ?? null, function (Builder $query, int $user_id): void {
                $query->where('user_id', $user_id);
            })
            ->when($filters['date_from'] ?? null, function (Builder $query, string $dateFrom): void {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function (Builder $query, string $dateTo): void {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($filters['tags'] ?? null, function (Builder $query, array $tags): void {
                $query->whereHas('tags', function (Builder $tagQuery) use ($tags) {
                    $tagQuery->whereIn('tags.id', $tags);
                });
            });
    }

    #[Scope]
    public function sort(Builder $query, ?string $sort): Builder
    {
        return $query
            ->when($sort ?? null, function (Builder $query, string $sort): Builder {
               return match($sort) {
                 'latest' => $query->latest(),
                 'oldest' => $query->oldest(),
                 'title_asc' => $query->orderBy('title', 'asc'),
                 'title_desc' => $query->orderBy('title', 'desc'),
                 'author_asc' => $query->orderBy(User::select('name')
                     ->whereColumn('users.id', 'stories.user_id'), 'asc'),
                 'author_desc' => $query->orderBy(User::select('name')
                     ->whereColumn('users.id', 'stories.user_id'), 'desc'),
                 default => $query->latest()
               };
            });
    }

    /*
     * Выборка последнего отклонения (причины отклонения истории)
     */
    public function lastRejectionModeration(): HasOne
    {
        return $this->hasOne(StoryModeration::class)
            ->where('action',StoryStatus::Rejected)
            ->latestOfMany();
    }

    /*
     * Касты для атрибутов истории
     */
    protected function casts(): array
    {
        return [
            'status' => StoryStatus::class,
            'published_at' => 'datetime',
            'moderated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
