<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 管理者ユーザーの作成
        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($this->admin);
    }

    /** @test */
    public function 勤怠詳細ページに正しいデータが表示される()
    {
        $user = \App\Models\User::factory()->create();

        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->startOfMonth()->toDateString(), // ← 月に合わせて明示
            'clock_in' => now()->setTime(9, 0),
            'clock_out' => now()->setTime(18, 0),
            'note' => 'テストメモ',
        ]);

        $response = $this->get(route('admin.attendance.staff', [
            'id' => $user->id,
            'month' => now()->format('Y-m'), // 対象月を指定
        ]));

        $response->assertStatus(200)
                ->assertSee('09:00')
                ->assertSee('18:00');
    }

    /** @test */
    public function 出勤時間が退勤時間より後の場合バリデーションエラーが出る()
    {
        $attendance = Attendance::factory()->create();

        $response = $this->post(route('admin.attendance.update', ['id' => $attendance->id]), [
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'note' => '修正',
        ]);

        $response->assertSessionHasErrors([
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です。',
        ]);
    }

    /** @test */
    public function 休憩開始時間が退勤時間より後の場合バリデーションエラーが出る()
    {
        $attendance = Attendance::factory()->create();

        $response = $this->post(route('admin.attendance.update', ['id' => $attendance->id]), [
            'clock_in' => '09:00',
            'clock_out' => '17:00',
            'break_start_1' => '18:00',
            'break_end_1' => '18:30',
            'note' => '修正',
        ]);

        $response->assertSessionHasErrors([
            'break_start_1' => '休憩時間が不適切な値です。',
        ]);
    }

    /** @test */
    public function 休憩終了時間が退勤時間より後の場合バリデーションエラーが出る()
    {
        $attendance = Attendance::factory()->create();

        $response = $this->post(route('admin.attendance.update', ['id' => $attendance->id]), [
            'clock_in' => '09:00',
            'clock_out' => '17:00',
            'break_start_1' => '16:00',
            'break_end_1' => '18:00',
            'note' => '修正',
        ]);

        $response->assertSessionHasErrors([
            'break_start_1' => '出勤時間もしくは退勤時間が不適切な値です。',
        ]);
    }

    /** @test */
    public function 備考未入力時にバリデーションエラーが出る()
    {
        $attendance = Attendance::factory()->create();

        $response = $this->post(route('admin.attendance.update', ['id' => $attendance->id]), [
            'clock_in' => '09:00',
            'clock_out' => '17:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください。',
        ]);
    }
}
