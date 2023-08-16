<?php

namespace Database\Factories;

use App\Http\Helpers\Utils;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupWorkoutFactory extends Factory
{
    protected $faker;

    public function definition(): array
    {
        $faker = app(Generator::class);

        return [
            'event' => Utils::randomDate((date('Y')-1).'-'.date('m').'-1',date('Y-m-d')),
            'cancelled' => false,
            'schedule_id' => $faker->numberBetween(1, 85),
            'reason'=> ""
        ];
    }
}
