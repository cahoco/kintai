<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsWithDate($user, $datetime = '2025-05-29 09:00:00')
    {
        Carbon::setTestNow(Carbon::parse($datetime));
        return $this->actingAs($user);
    }

    /** @test */
    public function 勤務外のユーザーが出勤しステータスが出勤中になる()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);

        $responseBefore = $this->get('/attendance');
        $responseBefore->assertSee('出勤');

        $this->post('/attendance/clock-in');

        $responseAfter = $this->get('/attendance');
        $responseAfter->assertSee('出勤中');
    }

    /** @test */
    public function 退勤済ユーザーには出勤ボタンが表示されない()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user, '2025-05-29 18:00:00');

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->get('/attendance');
        $response->assertDontSee('出勤');
    }

    /** @test */
    public function 管理者はユーザーの出勤時刻を確認できる()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $staff = User::factory()->create();

        Carbon::setTestNow(Carbon::create(2025, 5, 29, 9, 0));
        Attendance::create([
            'user_id' => $staff->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/staff/' . $staff->id);

        $response->assertSee('09:00');
    }
}
