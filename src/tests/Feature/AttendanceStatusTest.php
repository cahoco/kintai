<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsWithDate($user)
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 29, 9, 0)); // 日付固定
        return $this->actingAs($user);
    }

    /** @test */
    public function 勤務外のステータスが表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        $response = $this->get('/attendance');
        $response->assertSee('勤務外');
    }

    /** @test */
    public function 出勤中のステータスが表示される()
    {
        $user = User::factory()->create();
        Carbon::setTestNow(Carbon::create(2025, 5, 29, 9, 0));

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now()->subHours(1),
        ]);
        $this->actingAsWithDate($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    /** @test */
    public function 休憩中のステータスが表示される()
    {
        $user = User::factory()->create();
        Carbon::setTestNow(Carbon::create(2025, 5, 29, 9, 0));

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now()->subHours(2),
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now()->subMinutes(30),
            'break_end' => null,
        ]);
        $this->actingAsWithDate($user);
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    /** @test */
    public function 退勤済のステータスが表示される()
    {
        $user = User::factory()->create();
        Carbon::setTestNow(Carbon::create(2025, 5, 29, 9, 0));

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHours(1),
        ]);
        $this->actingAsWithDate($user);
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }
}
