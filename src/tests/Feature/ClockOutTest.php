<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsWithFixedTime($user, $hour = 9)
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 30, $hour, 0)); // 2025-05-30 09:00
        return $this->actingAs($user);
    }

    /** @test */
    public function 勤務中ユーザーが退勤するとステータスが退勤済になる()
    {
        $user = User::factory()->create();
        $this->actingAsWithFixedTime($user);
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now()->subHours(8)->format('H:i:s'),
        ]);

        // 退勤処理
        $response = $this->post('/attendance/clock-out');

        // ステータス確認画面
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    /** @test */
    public function 退勤時刻が管理画面に表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithFixedTime($user);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        // 管理者ユーザーで確認
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('18:00'); // 管理画面に表示されているか
    }
}
