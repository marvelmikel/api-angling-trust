<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Modules\FishingDraw\Entities\FishingDrawEntry;
use Modules\Members\Entities\Member;

/**
 * @var Factory $factory
 */
$factory->define(FishingDrawEntry::class, function (Faker $faker) {
    $member = null;

    if ($faker->boolean(25)) {
        $member = Member::inRandomOrder()->limit(1)->first();
    }

    $quantity = $faker->randomElement([1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 5, 6]);

    $first_name = $faker->firstName;
    $last_name = $faker->lastName;

    $purchased_at = $faker->dateTimeBetween('-10 day', 'now');

    $reference = $purchased_at->getTimestamp();
    $reference = $faker->randomNumber(3) . substr($reference, 3);

    return [
        'member_id' => $member ? $member->id : null,
        'reference' => $reference,
        'name' => $member ? $member->full_name : $first_name . ' ' . $last_name,
        'email' => $member ? $member->user->email : strtolower($first_name) . '.' . strtolower($last_name) . '@test.com',
        'quantity' => $quantity,
        'payment_amount' => 5 * $quantity * 100,
        'payment_id' => $faker->bothify('??#####?###?#####?'),
        'deleted_at' => $faker->boolean(10) ? now()->format('Y-m-d H:i:s') : null,
        'created_at' => $purchased_at->format('Y-m-d H:i:s'),
        'updated_at' => $purchased_at->format('Y-m-d H:i:s')
    ];
});
