<?php

namespace App\Console\Commands;

use App\Domains\HCM\Models\AttendanceLog;
use App\Domains\HCM\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncBiometricAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hcm:sync-biometrics {--mock : Run in mock mode without hitting the real API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync attendance logs from ZKBioTime API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Biometric Sync...");

        if ($this->option('mock')) {
            $this->runMockSync();
            return;
        }

        $apiUrl = config('services.zkbiotime.url', 'https://zkbiotime.example.com/api/iclock/transactions');
        $apiToken = config('services.zkbiotime.token', 'dummy-token');

        try {
            // In a real implementation, we would query from the last synced timestamp.
            // For now, we simulate the request.
            $response = Http::withToken($apiToken)->get($apiUrl, [
                'start_time' => now()->subMinutes(10)->toDateTimeString(),
                'end_time' => now()->toDateTimeString(),
            ]);

            if ($response->successful()) {
                $logs = $response->json('data', []);
                $this->processLogs($logs);
            } else {
                $this->error("API Request failed: " . $response->status());
                Log::error("ZKBioTime API failed", ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error("Connection error: " . $e->getMessage());
            Log::error("ZKBioTime connection error", ['exception' => $e]);
        }

        $this->info("Biometric Sync Completed.");
    }

    protected function processLogs(array $logs)
    {
        $count = 0;
        foreach ($logs as $logData) {
            // Find employee by employee_code (often mapped to ZKBioTime EMP_PIN)
            $employee = Employee::where('employee_code', $logData['emp_code'] ?? '')->first();
            
            if ($employee) {
                // Determine punch type (0=Check-in, 1=Check-out, etc in ZK systems)
                $punchType = ($logData['punch_state'] ?? 0) == 0 ? 'IN' : 'OUT';

                AttendanceLog::firstOrCreate([
                    'employee_id' => $employee->id,
                    'punch_time' => Carbon::parse($logData['punch_time']),
                ], [
                    'punch_type' => $punchType,
                    'biometric_device_id' => $logData['terminal_id'] ?? 'unknown',
                ]);
                $count++;
            }
        }
        $this->info("Successfully synced {$count} logs.");
    }

    protected function runMockSync()
    {
        $this->info("Running in MOCK mode.");
        
        $employees = Employee::all();
        if ($employees->isEmpty()) {
            $this->warn("No employees found to generate mock logs.");
            return;
        }

        $logs = [];
        foreach ($employees as $emp) {
            // Simulate an IN punch today at 9:00 AM
            $logs[] = [
                'emp_code' => $emp->employee_code,
                'punch_time' => now()->setTime(9, random_int(0, 15), 0)->toDateTimeString(),
                'punch_state' => 0, // IN
                'terminal_id' => 'DEV001',
            ];
        }

        $this->processLogs($logs);
    }
}
