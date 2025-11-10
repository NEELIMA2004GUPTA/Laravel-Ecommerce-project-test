<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class VerifyEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function redirects_if_email_already_verified()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // Generate a signed URL for the verification route
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user);

        $response = $this->get($url);

        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
    }

    #[Test]
    public function marks_email_as_verified_and_fires_event()
    {
        Event::fake();

        $user = User::factory()->create(['email_verified_at' => null]);

        // Generate a signed URL for the verification route
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user);

        $response = $this->get($url);

        // Refresh the user to get updated email_verified_at
        $user->refresh();

        $this->assertNotNull($user->email_verified_at);

        Event::assertDispatched(Verified::class, fn($event) => $event->user->id === $user->id);

        $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
    }
}
