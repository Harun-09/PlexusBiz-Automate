<?php

namespace App\Console\Commands;

use App\Domains\Core\Models\OutboxMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProcessOutboxMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbox:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending events from the outbox to ensure atomic inter-domain communication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // For production, this should run as a daemon or frequently via scheduler.
        // We select only unprocessed messages.
        $messages = OutboxMessage::where('processed', false)
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();

        if ($messages->isEmpty()) {
            $this->info("No pending outbox messages found.");
            return;
        }

        foreach ($messages as $message) {
            try {
                $eventClass = $message->event_type;
                if (!class_exists($eventClass)) {
                    throw new \Exception("Event class {$eventClass} does not exist.");
                }

                // If the event class accepts the payload array in its constructor, we instantiate it.
                // Alternatively, we can just fire it directly.
                // Assuming events take the payload array or can be dispatched with it.
                Event::dispatch(new $eventClass($message->payload));

                $message->update([
                    'processed' => true,
                    'processed_at' => now(),
                ]);

                $this->info("Processed outbox message: {$message->id}");
            } catch (\Exception $e) {
                Log::error("Failed to process outbox message {$message->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->error("Failed to process message {$message->id}: {$e->getMessage()}");
            }
        }
    }
}
