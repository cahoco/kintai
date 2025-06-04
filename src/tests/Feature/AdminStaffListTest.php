<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザーを作成
        $this->admin = User::factory()->create(['is_admin' => true]);

        // 一般ユーザーを複数作成
        User::factory()->count(3)->create(['is_admin' => false]);

        // 管理者としてログイン
        $this->actingAs($this->admin);
    }

    /** @test */
    public function 管理者は全ユーザーの氏名とメールアドレスを確認できる()
    {
        $response = $this->get('/admin/staff/list');

        $response->assertStatus(200);

        $users = User::where('is_admin', false)->get();

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /** @test */
    public function 管理者は指定ユーザーの勤怠一覧を確認できる()
    {
        $user = User::where('is_admin', false)->first();

        // 今月の勤怠を1件作成
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->get("/admin/attendance/staff/{$user->id}?month=" . now()->format('Y-m'));

        $response->assertStatus(200)
                ->assertSee('09:00')
                ->assertSee('18:00');
    }

    /** @test */
    public function 管理者は前月の勤怠情報を確認できる()
    {
        $user = User::where('is_admin', false)->first();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $lastMonth->copy()->addDays(3),
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $response = $this->get("/admin/attendance/staff/{$user->id}?month=" . $lastMonth->format('Y-m'));

        $response->assertStatus(200)
                ->assertSee('10:00')
                ->assertSee('19:00');
    }

    /** @test */
    public function 管理者は翌月の勤怠情報を確認できる()
    {
        $user = User::where('is_admin', false)->first();
        $nextMonth = Carbon::now()->addMonth()->startOfMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $nextMonth->copy()->addDays(5),
            'clock_in' => '08:30',
            'clock_out' => '17:30',
        ]);

        $response = $this->get("/admin/attendance/staff/{$user->id}?month=" . $nextMonth->format('Y-m'));

        $response->assertStatus(200)
                ->assertSee('08:30')
                ->assertSee('17:30');
    }

    /** @test */
    public function 管理者は勤怠一覧から詳細ページに遷移できる()
    {
        $user = User::where('is_admin', false)->first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => 'テストメモ',
        ]);

        // 勤怠詳細ページにアクセス
        $response = $this->get("/attendance/{$attendance->id}?from=staff");

        $response->assertStatus(200)
                ->assertSee('09:00')
                ->assertSee('18:00')
                ->assertSee('テストメモ');
    }
}
