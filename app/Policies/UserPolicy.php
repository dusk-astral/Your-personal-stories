<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\RedirectResponse;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $admin, User $user): bool
    {
        if ($admin->role !== UserRole::Admin) {
            return false;
        }

        if ($user->role != UserRole::Admin) {
            return true;
        }

        $activeOtherAdminsCount = User::query()
            ->where('role', UserRole::Admin)
            ->where('is_blocked', false)
            ->where('id', '!=', $user->id)
            ->count();

        return $activeOtherAdminsCount > 0;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $admin, User $user): bool
    {
        // Только админ может удалять
        if ($admin->role !== UserRole::Admin) {
            return false;
        }

        if ($user->role != UserRole::Admin) {
            return true;
        }

        $activeOtherAdminsCount = User::query()
            ->where('role', UserRole::Admin)
            ->where('is_blocked', false)
            ->where('id', '!=', $user->id)
            ->count();

        return $activeOtherAdminsCount > 0;
    }

    public function block(User $admin, User $user): bool
    {
        if ($admin->role !== UserRole::Admin) {
            return false;
        }

        if ($user->is_blocked) {
            return false;
        }

        if ($user->role !== UserRole::Admin) {
            return true;
        }

        $activeOtherAdminsCount = User::query()
            ->where('role', UserRole::Admin)
            ->where('is_blocked', false)
            ->where('id', '!=', $user->id)
            ->count();

        return $activeOtherAdminsCount > 0;
    }

    public function unblock(User $admin, User $user): bool
    {
        return $admin->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
