<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsWithDate($user, $date = '2025-05-01')
    {
        Carbon::setTestNow(Carbon::parse($date));
        return $this->actingAs($user);
    }

    /** @test */
    public function 勤怠詳細にログインユーザーの名前が表示される()
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);
        $this->actingAsWithDate($user);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
        ]);
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSee('テスト太郎');
    }

    /** @test */
    public function 勤怠詳細に正しい日付が表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
        ]);
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSeeTextInOrder(['2025年', '5月1日']);
    }

    /** @test */
    public function 勤怠詳細に出勤と退勤の時間が表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function 勤怠詳細に休憩時間が表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '12:30:00',
        ]);
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSee('12:00');
        $response->assertSee('12:30');
    }
}
