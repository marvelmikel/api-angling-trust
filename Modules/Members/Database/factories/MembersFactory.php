<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Modules\Members\Entities\Member;
use Modules\Members\Entities\MembershipType;

/**
 * @var Factory $factory
 */
$factory->define(Member::class, function (Faker $faker) {
    $first_name = $faker->firstName;
    $last_name = $faker->lastName;

    return [
        'first_name' => $first_name,
        'last_name' => $faker->lastName,
        'email' => strtolower($first_name) . '.' . strtolower($last_name) . '@test.com',
        'password' => 'password',
        'membership_type_id' => MembershipType::inRandomOrder()->limit(1)->first()->id,
        'preferences' => [
            'disciplines' => $faker->randomElements(['game', 'coarse', 'sea', 'kayak', 'recreation', 'match', 'specimen'], $faker->numberBetween(1, 7)),
            'division' => $faker->randomElement(['division_1', 'division_2', 'division_3']),
            'regions' => $faker->randomElements(['cornwall', 'east_anglia', 'essex', 'isle_of_wight'], $faker->numberBetween(1, 4))
        ]
    ];
});
