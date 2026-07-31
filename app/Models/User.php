<?php

namespace App\Models;

use App\Enums\StoryStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Трейт для инициализации фабрики к модели

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_blocked',
        'bio',
        'avatar_path',
    ];

    protected $hidden = ['password'];

    /*
     * Получение истории пользователя
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function moderations(): HasMany
    {
        return $this->hasMany(StoryModeration::class, 'moderator_id');
    }

    /*
     * Optional static
     */
    public function isBlocked(): bool
    {
        return $this->is_blocked === true || $this->is_blocked === 1;
    }
    public function storiesCount(): int
    {
        return $this->stories()->count();
    }

    public function approvedStoriesCount(): int
    {
        return $this->stories()->where('status', StoryStatus::Approved)->count();
    }

    /*
     * Касты для атрибутов пользователей
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }
}
