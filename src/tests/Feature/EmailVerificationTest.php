<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後に認証メールが送信される()
    {
        Notification::fake();
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function 認証画面に認証ボタンが表示される()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertStatus(200)
                ->assertSee('認証はこちらから');
    }

    /** @test */
    public function 認証リンクをクリックすると勤怠登録画面にリダイレクトされる()
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $response = $this->actingAs($user)->get($url);
        $response->assertRedirect('/attendance');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
