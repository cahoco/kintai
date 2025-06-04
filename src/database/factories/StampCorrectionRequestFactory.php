<?php

namespace Database\Factories;

use App\Models\StampCorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => $this->faker->sentence(),
            'status' => '承認待ち',
        ];
    }
}
