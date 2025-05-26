<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $clockIn = Carbon::createFromTime(9, 0)->addMinutes($this->faker->numberBetween(0, 30));
        $clockOut = Carbon::createFromTime(18, 0)->addMinutes($this->faker->numberBetween(0, 30));
        $date = Carbon::today()->subDays($this->faker->numberBetween(0, 10))->toDateString();

        $user = User::inRandomOrder()->first();
        $userId = $user ? $user->id : 1;

        return [
            'user_id'   => $userId,
            'date'      => $date,
            'clock_in'  => $clockIn,
            'clock_out' => $clockOut,
            'note'      => $this->faker->randomElement(['遅延対応', '業務開始', '通常出勤']),
        ];
    }

}
