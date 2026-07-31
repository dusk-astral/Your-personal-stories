<?php

namespace App\Policies;

use App\Models\Story;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Enums\UserRole;
use App\Enums\StoryStatus;
use Illuminate\Http\RedirectResponse;

class StoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Story $story): Response
    {
        return $story->status === StoryStatus::Approved || ($user?->id == $story->user_id)
            || ($user?->role == UserRole::Moderator || UserRole::Admin)
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Moderator, UserRole::Admin, UserRole::User], true);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Story $story): Response
    {
        return $user->id === $story->user_id || $user->role == UserRole::Admin
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Story $story): Response
    {
        return $user->id === $story->user_id || $user->role == UserRole::Admin
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Story $story): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Story $story): bool
    {
        return false;
    }

    /*
     * Методы для модерации
     */
    public function approve(User $user, Story $story): bool
    {
        return in_array($user->role, [UserRole::Moderator, UserRole::Admin], true);
    }

    public function reject(User $user, Story $story): bool
    {
        return in_array($user->role, [UserRole::Moderator, UserRole::Admin], true);
    }

    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }
        return null;
    }
}
