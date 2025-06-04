<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminStampCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function 承認待ちの修正申請が一覧に表示される()
    {
        StampCorrectionRequest::factory()->count(3)->create([
            'status' => '承認待ち',
            'user_id' => $this->user->id,
        ]);
        $response = $this->get('/stamp_correction_request/list?tab=pending');
        $response->assertStatus(200);
        $requests = StampCorrectionRequest::where('status', 'pending')->get();
        foreach ($requests as $request) {
            $response->assertSee((string) $request->id);
            $response->assertSee($request->note);
        }
    }

    /** @test */
    public function 承認済みの修正申請が一覧に表示される()
    {
        StampCorrectionRequest::factory()->count(2)->create([
            'status' => '承認済み',
            'user_id' => $this->user->id,
        ]);
        $response = $this->get('/stamp_correction_request/list?tab=approved');
        $response->assertStatus(200);
        $requests = StampCorrectionRequest::where('status', 'approved')->get();
        foreach ($requests as $request) {
            $response->assertSee((string) $request->id);
            $response->assertSee($request->note);
        }
    }

    /** @test */
    public function 修正申請の詳細が正しく表示される()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'note' => '申請のメモです',
        ]);
        $request = StampCorrectionRequest::factory()->create([
            'user_id' => $this->user->id,
            'attendance_id' => $attendance->id,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '申請のメモです',
        ]);
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertStatus(200)
                ->assertSee('09:00')
                ->assertSee('18:00')
                ->assertSee('申請のメモです');
    }

    /** @test */
    public function 修正申請を承認すると勤怠情報が更新される()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'clock_in' => '10:00',
            'clock_out' => '17:00',
        ]);
        $request = StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $this->user->id,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'status' => '承認待ち',
        ]);
        $response = $this->post("/stamp_correction_request/approve/{$request->id}");
        $response->assertRedirect();
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'status' => '承認済み',
        ]);
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
    }
}
