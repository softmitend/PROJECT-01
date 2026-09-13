<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LineLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.line', [
            'channel_id' => '1234567890',
            'channel_secret' => 'line-secret',
            'redirect' => 'http://localhost/auth/line/callback',
        ]);
    }

    public function test_guest_sees_line_login_inside_profile_page(): void
    {
        $this->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Login dengan LINE')
            ->assertSee(route('line-auth.redirect'), false)
            ->assertSee('Profil');
    }

    public function test_line_redirect_uses_state_nonce_and_pkce(): void
    {
        $response = $this->get(route('line-auth.redirect'));

        $response->assertRedirectContains('https://access.line.me/oauth2/v2.1/authorize');
        $response->assertSessionHas('line_oauth_state');
        $response->assertSessionHas('line_oauth_nonce');
        $response->assertSessionHas('line_oauth_verifier');
        $this->assertStringContainsString('code_challenge_method=S256', $response->headers->get('Location'));
    }

    public function test_verified_line_identity_creates_member_session(): void
    {
        Http::fake([
            'https://api.line.me/oauth2/v2.1/token' => Http::response([
                'access_token' => 'access-token',
                'id_token' => 'signed-id-token',
                'token_type' => 'Bearer',
            ]),
            'https://api.line.me/oauth2/v2.1/verify' => Http::response([
                'iss' => 'https://access.line.me',
                'sub' => 'U1234567890abcdef',
                'aud' => '1234567890',
                'nonce' => 'known-nonce',
                'name' => 'Caca',
                'picture' => 'https://profile.line-scdn.net/example',
            ]),
        ]);

        $response = $this->withSession([
            'line_oauth_state' => 'known-state',
            'line_oauth_nonce' => 'known-nonce',
            'line_oauth_verifier' => str_repeat('a', 64),
        ])->get(route('line-auth.callback', [
            'state' => 'known-state',
            'code' => 'authorization-code',
        ]));

        $response->assertRedirect(route('profile.show'));
        $this->assertAuthenticated();

        $member = Member::where('line_user_id', 'U1234567890abcdef')->sole();
        $user = User::where('line_user_id', 'U1234567890abcdef')->sole();

        $this->assertSame('Caca', $member->display_name);
        $this->assertSame('member', $user->role);
        $this->assertSame($member->id, $user->member_id);
    }

    public function test_invalid_oauth_state_does_not_create_a_session(): void
    {
        $this->withSession(['line_oauth_state' => 'expected'])
            ->get(route('line-auth.callback', ['state' => 'different', 'code' => 'code']))
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasErrors('line');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
