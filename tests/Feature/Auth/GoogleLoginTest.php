<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function fakeGoogleUser(string $email, string $id = 'google-123'): void
    {
        $googleUser = (new GoogleUser)->map([
            'id' => $id,
            'name' => 'Pengguna Google',
            'email' => $email,
            'avatar' => 'https://lh3.googleusercontent.com/a/foto',
        ]);

        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('user')->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_login_page_shows_google_button_only(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Lanjutkan dengan Google')
            ->assertSee(route('auth.google'), false)
            ->assertDontSee('facebook', false)
            ->assertDontSee('linkedin', false);
    }

    public function test_unregistered_google_account_is_rejected(): void
    {
        $this->fakeGoogleUser('orang.asing@gmail.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'orang.asing@gmail.com']);
    }

    public function test_registered_google_account_logs_in_to_its_panel(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $client = User::factory()->create(['email' => 'klien@gmail.com']);
        $client->assignRole('client');

        $this->fakeGoogleUser('klien@gmail.com', 'google-999');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('client.dashboard'));

        $this->assertAuthenticatedAs($client);
        $this->assertSame('google-999', $client->fresh()->google_id);
    }
}
