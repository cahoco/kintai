<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 管理者ログイン_メールアドレスが未入力の場合はエラーが表示される()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('メールアドレスを入力してください', session('errors')->first('email'));
    }

    /** @test */
    public function 管理者ログイン_パスワードが未入力の場合はエラーが表示される()
    {
        $user = User::factory()->create(['is_admin' => true]);
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => '',
        ]);
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードを入力してください', session('errors')->first('password'));
    }

    /** @test */
    public function 管理者ログイン_認証情報が間違っていればエラーメッセージが表示される()
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/admin/login');
        $response = $this->followRedirects($response);
        $response->assertSee('ログイン情報が登録されていません');
    }

}
