<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => bcrypt('00000000'),
                'is_admin' => true,
            ]
        );
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テスト',
                'password' => bcrypt('00000000'),
                'is_admin' => false,
            ]
        );
        \App\Models\User::factory()->count(10)->create();
        $this->call(AttendanceSeeder::class);
    }

}
