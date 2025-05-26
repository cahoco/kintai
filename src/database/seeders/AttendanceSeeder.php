<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('is_admin', false)->get();

        // 過去90日分
        foreach (range(0, 89) as $i) {
            $date = Carbon::today()->subDays($i)->toDateString();

            // 毎日 1〜10人の出勤者をランダムに選出（重複なし）
            $dayUsers = $users->shuffle()->take(rand(1, min(10, $users->count())));

            foreach ($dayUsers as $user) {
                $clockIn = Carbon::createFromFormat('Y-m-d H:i', "$date 09:00")->addMinutes(rand(0, 30));
                $clockOut = Carbon::createFromFormat('Y-m-d H:i', "$date 18:00")->addMinutes(rand(0, 30));

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                ]);

                // 0〜2回の休憩（30〜90分）をランダムで生成
                $breakCount = rand(0, 2);

                for ($j = 0; $j < $breakCount; $j++) {
                    $breakStart = $clockIn->copy()->addMinutes(rand(120, 360)); // 出勤から2〜6時間後
                    $breakDuration = rand(30, 90);
                    $breakEnd = $breakStart->copy()->addMinutes($breakDuration);

                    if ($breakEnd->gt($clockOut)) {
                        $breakEnd = $clockOut->copy();
                    }

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStart,
                        'break_end' => $breakEnd,
                    ]);
                }
            }
        }
    }
}
