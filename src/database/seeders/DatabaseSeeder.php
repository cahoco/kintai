<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

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
                'email_verified_at' => Carbon::now(),
            ]
        );
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テスト',
                'password' => bcrypt('00000000'),
                'is_admin' => false,
                'email_verified_at' => Carbon::now(),
            ]
        );
        \App\Models\User::factory()->count(10)->create();
        $this->call(AttendanceSeeder::class);
    }

}
