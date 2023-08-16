<?php

namespace Database\Factories;

use App\Http\Helpers\Utils;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoachFactory extends Factory
{
    protected $faker;

    public function definition(): array
    {
        $faker = app(Generator::class);

        $gender = $faker->randomElements(['male', 'female'])[0];

        $p = $faker->randomElements(Utils::$patronymic)[0];
        $p .= ($gender == 'male') ? "ич" : "на";

        return [

            'surname' =>  $faker->lastName($gender),
            'name'=> $faker->firstName($gender),
            'patronymic' =>  $p,
            'passport' => $faker->isbn10,
            'birth' => $faker->date('Y-m-d', '2001-12-01'),
            'mail' => $faker->freeEmail,
            'number' => $faker->phoneNumber,
            'registration' => $faker->address,

        ];
    }
}
