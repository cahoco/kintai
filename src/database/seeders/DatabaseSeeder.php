<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
{
    // 管理者1人（明示的に作成）
    User::factory()->create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('00000000'), // ログインテスト用
        'is_admin' => true,
    ]);

    // 一般ユーザー（ログイン用）
    User::factory()->create([
        'name' => 'テスト',
        'email' => 'test@example.com',
        'password' => bcrypt('00000000'), // ログイン用パスワード
        'is_admin' => false,
    ]);

    // 一般ユーザー10人（is_admin: false はFactoryで既定）
    User::factory()->count(10)->create();
}

}
