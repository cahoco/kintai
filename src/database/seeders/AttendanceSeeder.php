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
        foreach (range(0, 89) as $i) {
            $date = Carbon::today()->subDays($i)->toDateString();
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
                $breakCount = rand(0, 2);
                for ($j = 0; $j < $breakCount; $j++) {
                    $breakStart = $clockIn->copy()->addMinutes(rand(120, 360));
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
