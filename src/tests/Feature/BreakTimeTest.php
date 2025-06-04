<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BreakTimeTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsWithDate($user, $datetime = '2025-05-29 09:00:00')
    {
        Carbon::setTestNow(Carbon::parse($datetime));
        return $this->actingAs($user);
    }

    /** @test */
    public function 出勤中のユーザーが休憩入できステータスが休憩中になる()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $responseBefore = $this->get('/attendance');
        $responseBefore->assertSee('休憩入');

        $this->post('/attendance/break-start');

        $responseAfter = $this->get('/attendance');
        $responseAfter->assertSee('休憩中');
    }

    /** @test */
    public function 休憩は一日に何回でも可能で再度休憩入できる()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        // 1回目の休憩入・戻
        $this->post('/attendance/break-start');
        $this->post('/attendance/break-end');

        // 再度の休憩入
        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    /** @test */
    public function 休憩戻ボタンが機能しステータスが出勤中に戻る()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $this->post('/attendance/break-start');

        $responseDuring = $this->get('/attendance');
        $responseDuring->assertSee('休憩戻');

        $this->post('/attendance/break-end');

        $responseAfter = $this->get('/attendance');
        $responseAfter->assertSee('出勤中');
    }

    /** @test */
    public function 複数回の休憩戻も正しく動作し再度ボタンが表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        // 1回目
        $this->post('/attendance/break-start');
        $this->post('/attendance/break-end');

        // 2回目
        $this->post('/attendance/break-start');
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    /** @test */
    public function 勤怠一覧に休憩時刻が正しく表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $attendance->breakTimes()->create([
            'break_start' => '12:00:00',
            'break_end' => '12:30:00',
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertSee('0:30');
    }
}
