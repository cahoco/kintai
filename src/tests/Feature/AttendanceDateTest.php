<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceDateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 勤怠打刻画面に現在の日時が表示されている()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 29, 18, 32));
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('2025年5月29日(木)');
        $response->assertSee('18:32');
    }
}
