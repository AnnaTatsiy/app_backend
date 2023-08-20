<?php

namespace Database\Factories;

use App\Http\Helpers\Utils;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $faker;

    public function definition(): array
    {
        $faker = app(Generator::class);

        $gender = $faker->randomElements(['male', 'female'])[0];

        $patronymic = $faker->randomElements(Utils::$patronymic)[0];
        $patronymic .= ($gender == 'male') ? "ич" : "на";

        $name = $faker->firstName($gender);
        $email = $faker->freeEmail;

        $user = User::create(
            [
                'name' => $name . " " . $patronymic,
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]
        );

        return [

            'surname' =>  $faker->lastName($gender),
            'name'=> $name,
            'patronymic' =>  $patronymic,
            'passport' => $faker->isbn10,
            'birth' => $faker->date('Y-m-d', '2001-12-01'),
            'mail' => $email,
            'number' => $faker->phoneNumber,
            'user_id' => $user->id,
            'registration' => $faker->address,

        ];
    }
}
