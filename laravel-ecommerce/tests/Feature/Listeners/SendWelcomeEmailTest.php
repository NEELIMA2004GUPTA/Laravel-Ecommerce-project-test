<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SendWelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_welcome_email_notification()
    {
        Notification::fake();

        $user = User::factory()->create();

        // Send the notification
        $user->notify(new WelcomeEmail());

        // Assert the notification was sent
        Notification::assertSentTo($user, WelcomeEmail::class);
    }
}

