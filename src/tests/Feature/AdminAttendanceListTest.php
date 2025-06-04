<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザーを作成
        $this->admin = User::factory()->create([
            'is_admin' => true, // adminミドルウェアがこれで判定されている前提
        ]);
    }

    /** @test */
    public function 管理者がその日の全ユーザーの勤怠情報を確認できる()
    {
        $today = Carbon::today();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);
        }

        $response = $this->actingAs($this->admin)->get('/admin/attendance/list');

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee('09:00');
            $response->assertSee('18:00');
        }
    }

    /** @test */
    public function 管理者が勤怠一覧画面に現在日付を確認できる()
    {
        $today = Carbon::today()->format('Y年n月j日');

        $response = $this->actingAs($this->admin)->get('/admin/attendance/list');

        $response->assertStatus(200)
                ->assertSee($today);
    }

    /** @test */
    public function 管理者が前日の勤怠情報を確認できる()
    {
        $yesterday = Carbon::yesterday();
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $yesterday,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/attendance/list?date=' . $yesterday->toDateString());

        $response->assertStatus(200)
                ->assertSee('10:00')
                ->assertSee('19:00')
                ->assertSee($yesterday->format('Y年n月j日'));
    }

    /** @test */
    public function 管理者が翌日の勤怠情報を確認できる()
    {
        $tomorrow = Carbon::tomorrow();
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $tomorrow,
            'clock_in' => '08:30',
            'clock_out' => '17:30',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/attendance/list?date=' . $tomorrow->toDateString());

        $response->assertStatus(200)
                ->assertSee('08:30')
                ->assertSee('17:30')
                ->assertSee($tomorrow->format('Y年n月j日'));
    }
}
