<?php

namespace Tests\Feature;

use App\Domains\HCM\Models\AttendanceLog;
use App\Domains\HCM\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BiometricSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_biometric_sync_mock_mode()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'ZKB-001',
            'basic_salary' => 10000,
            'joining_date' => now(),
        ]);

        Artisan::call('hcm:sync-biometrics', ['--mock' => true]);

        $this->assertDatabaseHas('attendance_logs', [
            'employee_id' => $employee->id,
            'punch_type' => 'IN',
        ]);
    }

    public function test_biometric_sync_http_fake()
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'ZKB-002',
            'basic_salary' => 10000,
            'joining_date' => now(),
        ]);

        $punchTime = now()->subMinutes(5)->toDateTimeString();

        Http::fake([
            '*iclock/transactions*' => Http::response([
                'data' => [
                    [
                        'emp_code' => 'ZKB-002',
                        'punch_time' => $punchTime,
                        'punch_state' => 1, // OUT
                        'terminal_id' => 'DOOR-01',
                    ]
                ]
            ], 200)
        ]);

        Artisan::call('hcm:sync-biometrics');

        $this->assertDatabaseHas('attendance_logs', [
            'employee_id' => $employee->id,
            'punch_type' => 'OUT',
            'punch_time' => $punchTime,
            'biometric_device_id' => 'DOOR-01',
        ]);
    }
}
