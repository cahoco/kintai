<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが未入力の場合はバリデーションエラーが表示される()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('メールアドレスを入力してください', session('errors')->first('email'));
    }

    /** @test */
    public function パスワードが未入力の場合はバリデーションエラーが表示される()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => '',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードを入力してください', session('errors')->first('password'));
    }

    /** @test */
    public function ログイン情報が一致しない場合はエラーメッセージが表示される()
    {
        $user = User::factory()->create([
            'email' => 'correct@example.com',
            'password' => bcrypt('password123'),
        ]);
        $response = $this->from('/login')->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('ログイン情報が登録されていません', session('errors')->first('email'));
    }
}
