<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_if_email_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    #[Test]
    public function it_sends_verification_if_email_not_verified()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[Test]
    public function prompt_redirects_if_email_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function prompt_shows_view_if_email_not_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    #[Test]
    public function notification_invoke_redirects_if_email_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function notification_invoke_sends_email_if_not_verified()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[Test]
    public function notification_store_redirects_if_email_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));
        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function notification_store_sends_email_if_not_verified()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }
    
}
