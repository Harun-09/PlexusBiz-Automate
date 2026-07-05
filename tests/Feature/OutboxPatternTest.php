<?php

namespace Tests\Feature;

use App\Domains\Core\Models\OutboxMessage;
use App\Domains\Core\Services\OutboxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DummyEvent
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
}

class OutboxPatternTest extends TestCase
{
    use RefreshDatabase;

    public function test_outbox_service_saves_event()
    {
        $outboxService = new OutboxService();
        $payload = ['order_id' => 123, 'amount' => 500];

        $message = $outboxService->saveEvent(DummyEvent::class, $payload);

        $this->assertDatabaseHas('outbox_messages', [
            'id' => $message->id,
            'event_type' => DummyEvent::class,
            'processed' => false,
        ]);

        $this->assertEquals($payload, $message->payload);
    }

    public function test_outbox_command_processes_messages()
    {
        Event::fake();

        $outboxService = new OutboxService();
        $payload = ['order_id' => 124, 'amount' => 600];
        $message = $outboxService->saveEvent(DummyEvent::class, $payload);

        Artisan::call('outbox:process');

        Event::assertDispatched(DummyEvent::class, function ($event) use ($payload) {
            return $event->data === $payload;
        });

        $this->assertDatabaseHas('outbox_messages', [
            'id' => $message->id,
            'processed' => true,
        ]);
    }
}
