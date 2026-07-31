<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Преобразуем строки из маршрута в enum-значения
        $allowedRoles = collect($roles)
            ->map(fn (string $role):UserRole => UserRole::from($role)) // Итерируем каждое значение коллекции и передаём в замыкание
            ->all(); // Возвращаем базовый массив PHP

        $userRole = $user->role instanceof UserRole
            ? $user->role
            : UserRole::from($user->role);

        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
