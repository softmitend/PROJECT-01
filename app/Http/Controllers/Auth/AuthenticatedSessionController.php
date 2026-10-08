<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login', ['adminLogin' => request()->routeIs('admin.login')]);
    }

    public function store(Request $request)
    {
        $adminLogin = $request->routeIs('admin.login.store');
        $field = $adminLogin ? 'email' : 'username';
        $request->merge([$field => mb_strtolower(trim((string) $request->input($field)))]);
        $credentials = $request->validate([
            $field => ['required', 'string', 'max:255', ...($adminLogin ? ['email'] : [])],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attemptWhen(
            $adminLogin ? $credentials + ['role' => 'admin'] : $credentials,
            fn ($user) => $user->isAdmin() || ($user->role === 'customer' && $user->password_set_at !== null && $user->member?->is_active),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                $field => 'Username/email atau password tidak sesuai, atau akun belum aktif.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('login_at', now()->timestamp);

        return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('profile.show'));
    }

    public function destroy(Request $request)
    {
        $redirectTo = $request->user()?->isAdmin() ? '/admin/login' : '/login';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return new RedirectResponse($redirectTo, 303);
    }
}
