<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Mail\OrderConfirmedMail;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;

class OrderConfirmedMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_order_confirmed_mail_correctly()
    {
        $order = Order::factory()->create();

        $mailable = new OrderConfirmedMail($order);

        $mailable->build();

        // Subject
        $this->assertEquals('Order Confirmed - #' . $order->id, $mailable->subject);

        // View
        $this->assertEquals('emails.order-confirmed', $mailable->view);

       
        $this->assertEquals($order->id, $mailable->order->id);

    }

    #[Test]
    public function it_can_be_sent()
    {
        Mail::fake();

        $order = Order::factory()->create();

        Mail::to('test@example.com')->send(new OrderConfirmedMail($order));

        Mail::assertSent(OrderConfirmedMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }
}

