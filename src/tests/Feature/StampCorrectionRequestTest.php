<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StampCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $attendance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['name' => 'テスト太郎']);
        $this->attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-05-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
    }

    /** @test */
    public function 出勤時間が退勤時間より後ならバリデーションエラー()
    {
        $response = $this->actingAs($this->user)->post("/attendance/{$this->attendance->id}/request", [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'note' => 'テスト',
        ]);
        $response->assertSessionHasErrors([
        'clock_out' => '出勤時間もしくは退勤時間が不適切な値です。',
        ]);
    }

    /** @test */
    public function 休憩開始時間が退勤時間より後ならバリデーションエラー()
    {
        $response = $this->actingAs($this->user)
            ->post("/attendance/{$this->attendance->id}/request", [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'break_start_1' => '19:00',
                'break_end_1' => '19:30',
                'note' => 'テスト',
            ]);
        $response->assertSessionHasErrors([
        'break_start_1' => '休憩時間が不適切な値です。',
        ]);
    }

    /** @test */
    public function 休憩終了時間が退勤時間より後ならバリデーションエラー()
    {
        $response = $this->actingAs($this->user)
            ->post("/attendance/{$this->attendance->id}/request", [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'break_start_1' => '17:00',
                'break_end_1' => '19:30',
                'note' => 'テスト',
            ]);
        $response->assertSessionHasErrors([
        'break_end_1' => '出勤時間もしくは退勤時間が不適切な値です。',
        ]);
    }

    /** @test */
    public function 備考が未入力ならバリデーションエラー()
    {
        $response = $this->actingAs($this->user)
            ->post("/attendance/{$this->attendance->id}/request", [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);
        $response->assertSessionHasErrors([
        'note' => '備考を記入してください。',
        ]);
    }

    /** @test */
    public function 修正申請が作成される()
    {
        $this->actingAs($this->user)
            ->post("/attendance/{$this->attendance->id}/request", [
                'clock_in' => '09:30',
                'clock_out' => '18:00',
                'note' => '寝坊しました',
            ]);
        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $this->attendance->id,
            'clock_in' => '09:30:00',
            'note' => '寝坊しました',
        ]);
    }

    /** @test */
    public function 自分の申請が申請一覧に表示される()
    {
        StampCorrectionRequest::factory()->create([
            'user_id' => $this->user->id,
            'note' => 'テスト申請',
            'status' => '承認待ち',
        ]);
        $response = $this->actingAs($this->user)->get('/stamp_correction_request/list');
        $response->assertSee('テスト申請');
    }

    /** @test */
    public function 承認済み申請が一覧に表示される()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        StampCorrectionRequest::factory()->create([
            'user_id' => $admin->id,
            'note' => '承認された申請',
            'status' => '承認済み',
        ]);
        $response = $this->actingAs($admin)->get('/stamp_correction_request/list?status=承認済み');
        $response->assertSee('承認された申請');
        $response->assertSee('承認済み');
    }

    /** @test */
    public function 詳細ボタンで申請詳細に遷移できる()
    {
        $request = StampCorrectionRequest::factory()->create([
            'user_id' => $this->user->id,
            'note' => '詳細テスト申請',
        ]);
        $response = $this->actingAs($this->user)->get("/attendance/{$request->attendance_id}");
        $response->assertStatus(200)->assertSee('詳細テスト申請');
    }
}
