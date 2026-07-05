<?php

namespace App\Domains\CRM\Jobs;

use App\Domains\CRM\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateLeadScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Lead $lead
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $score = 10; // Base score

        if ($this->lead->email) {
            $score += 15;
        }
        if ($this->lead->phone) {
            $score += 10;
        }
        if ($this->lead->value > 1000) {
            $score += 20;
        }
        if ($this->lead->company_name) {
            $score += 15;
        }
        
        // Example: check customer profile completeness
        if ($this->lead->customer) {
            $score += 30; // High intent
        }

        $this->lead->update(['score' => $score]);
        
        Log::info("Calculated score {$score} for Lead ID {$this->lead->id}");
    }
}
