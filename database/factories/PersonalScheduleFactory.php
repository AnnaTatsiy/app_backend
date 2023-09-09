<?php

namespace Database\Factories;

use App\Http\Helpers\Utils;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalScheduleFactory extends Factory
{
    protected $faker;

    public function definition(): array
    {
        $faker = app(Generator::class);

        $time_begin = str_pad(rand(9,20), 2, 0, STR_PAD_LEFT);

        return [
            'day_id' => $faker->numberBetween(1, 7),
            'time_begin' => $time_begin.':00',
            'coach_id' => $faker->numberBetween(1, Utils::$count_coaches),
        ];
    }
}