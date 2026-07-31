<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /*
     *  Аутентификация пользователя
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember'); // Запомнить меня

        $user = User::where('email', $credentials['email'])->first();

        if ($user->is_blocked) {
            return back()->withErrors(['email' => 'Your account has been blocked.']);
        }

        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

           return redirect()->intended(route('stories.index'));
        }

        return back()->withErrors([
            'email' => 'Неверные данные входа.',
        ])->onlyInput('email');
    }

    /*
     * Выход пользователя
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('stories.index');
    }
}
