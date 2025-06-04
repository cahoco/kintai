<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsWithDate($user, $date = '2025-05-01')
    {
        Carbon::setTestNow(Carbon::parse($date));
        return $this->actingAs($user);
    }

    /** @test */
    public function 自分の勤怠情報がすべて表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        Attendance::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
        ]);
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('m/d'));
    }

    /** @test */
    public function 現在の月が表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        $response = $this->get('/attendance/list');
        $response->assertSee(Carbon::now()->format('Y/m'));
    }

    /** @test */
    public function 前月ボタンで前月の勤怠が表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user, '2025-05-15');
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-04-10',
        ]);
        $response = $this->get('/attendance/list?month=2025-04');
        $response->assertSee('04/10');
    }

    /** @test */
    public function 翌月ボタンで翌月の勤怠が表示される()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user, '2025-05-15');
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-10',
        ]);
        $response = $this->get('/attendance/list?month=2025-06');
        $response->assertSee('06/10');
    }

    /** @test */
    public function 詳細ボタンで勤怠詳細に遷移できる()
    {
        $user = User::factory()->create();
        $this->actingAsWithDate($user);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
        ]);
        $response = $this->get('/attendance/list');
        $response->assertSee('/attendance/' . $attendance->id);
    }
}
