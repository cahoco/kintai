<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 管理者1人（重複チェック付き）
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => bcrypt('00000000'),
                'is_admin' => true,
            ]
        );

        // 一般ユーザー（重複チェック付き）
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テスト',
                'password' => bcrypt('00000000'),
                'is_admin' => false,
            ]
        );

        // ダミー一般ユーザー追加
        \App\Models\User::factory()->count(10)->create();

        // ✅ 勤怠データ＋休憩データのSeederを実行
        $this->call(AttendanceSeeder::class);
    }
}
