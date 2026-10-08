<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureActiveCustomer
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && ! $user->isAdmin() && $user->role !== 'customer') {
            abort(403);
        }
        if ($user && ! $user->isAdmin()
            && (! $user->member?->is_active || $user->password_set_at === null)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['username' => 'Akun belum aktif. Hubungi admin untuk menyiapkan akun login.']);
        }

        return $next($request);
    }
}
