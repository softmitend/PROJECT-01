<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class LineAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('profile.show')->withErrors([
                'line' => 'LINE Login belum dikonfigurasi oleh admin.',
            ]);
        }

        if ($request->user()?->isAdmin()) {
            return redirect()->route('profile.show')->withErrors([
                'line' => 'Logout dari akun admin sebelum masuk sebagai member LINE.',
            ]);
        }

        $state = Str::random(48);
        $nonce = Str::random(48);
        $verifier = Str::random(96);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        $request->session()->put([
            'line_oauth_state' => $state,
            'line_oauth_nonce' => $nonce,
            'line_oauth_verifier' => $verifier,
        ]);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.line.channel_id'),
            'redirect_uri' => config('services.line.redirect'),
            'state' => $state,
            'scope' => 'openid profile',
            'nonce' => $nonce,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away('https://access.line.me/oauth2/v2.1/authorize?'.$query);
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return $this->failed('LINE Login belum dikonfigurasi oleh admin.');
        }

        $storedState = $request->session()->pull('line_oauth_state');
        $nonce = $request->session()->pull('line_oauth_nonce');
        $verifier = $request->session()->pull('line_oauth_verifier');
        $receivedState = $request->string('state')->toString();

        if (! $storedState || ! $receivedState || ! hash_equals($storedState, $receivedState)) {
            return $this->failed('Sesi LINE Login tidak valid atau sudah kedaluwarsa. Silakan coba kembali.');
        }

        if ($request->filled('error')) {
            return $this->failed('Login dengan LINE dibatalkan.');
        }

        if (! $request->filled('code') || ! $nonce || ! $verifier) {
            return $this->failed('Data autentikasi LINE tidak lengkap. Silakan coba kembali.');
        }

        try {
            $tokenResponse = Http::asForm()->acceptJson()->timeout(15)->post(
                'https://api.line.me/oauth2/v2.1/token',
                [
                    'grant_type' => 'authorization_code',
                    'code' => $request->string('code')->toString(),
                    'redirect_uri' => config('services.line.redirect'),
                    'client_id' => config('services.line.channel_id'),
                    'client_secret' => config('services.line.channel_secret'),
                    'code_verifier' => $verifier,
                ]
            );

            if ($tokenResponse->failed() || ! $tokenResponse->json('id_token')) {
                return $this->failed('LINE tidak dapat memverifikasi proses login. Silakan coba kembali.');
            }

            $identityResponse = Http::asForm()->acceptJson()->timeout(15)->post(
                'https://api.line.me/oauth2/v2.1/verify',
                [
                    'id_token' => $tokenResponse->json('id_token'),
                    'client_id' => config('services.line.channel_id'),
                    'nonce' => $nonce,
                ]
            );

            if ($identityResponse->failed() || ! $identityResponse->json('sub')) {
                return $this->failed('Identitas akun LINE tidak valid. Silakan coba kembali.');
            }

            $identity = $identityResponse->json();
            $user = DB::transaction(fn () => $this->resolveUser($identity));

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->route('profile.show')->with('status', 'Berhasil masuk dengan LINE.');
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed('LINE Login sedang tidak dapat dihubungi. Silakan coba beberapa saat lagi.');
        }
    }

    private function resolveUser(array $identity): User
    {
        $lineUserId = (string) $identity['sub'];
        $displayName = trim((string) ($identity['name'] ?? 'Member Ocean Paws')) ?: 'Member Ocean Paws';
        $avatarUrl = filled($identity['picture'] ?? null) ? (string) $identity['picture'] : null;

        $member = Member::query()->firstOrNew(['line_user_id' => $lineUserId]);
        if (! $member->exists) {
            $member->member_code = $this->generateMemberCode();
        }
        $member->fill([
            'display_name' => $displayName,
            'avatar_url' => $avatarUrl,
            'is_active' => true,
        ])->save();

        $user = User::query()->firstOrNew(['line_user_id' => $lineUserId]);
        $user->forceFill([
            'member_id' => $member->id,
            'name' => $displayName,
            'email' => 'line-'.hash('sha256', $lineUserId).'@members.oceanpaws.local',
            'password' => Str::random(64),
            'role' => 'member',
            'avatar_url' => $avatarUrl,
        ])->save();

        return $user;
    }

    private function generateMemberCode(): string
    {
        do {
            $code = 'MBR-LINE-'.Str::upper(Str::random(10));
        } while (Member::query()->where('member_code', $code)->exists());

        return $code;
    }

    private function isConfigured(): bool
    {
        return filled(config('services.line.channel_id'))
            && filled(config('services.line.channel_secret'))
            && filled(config('services.line.redirect'));
    }

    private function failed(string $message): RedirectResponse
    {
        return redirect()->route('profile.show')->withErrors(['line' => $message]);
    }
}
