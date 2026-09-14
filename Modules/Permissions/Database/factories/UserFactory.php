<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\User;

/**
 * Call this factory to create fake users.
 */
$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name'        => $faker->firstName,
        'last_name'         => $faker->lastName,
        'username'          => $faker->unique()->userName,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => \Hash::make('password'),
        'remember_token'    => Str::random(10),
        'verification_code' => 'VERIFIED',
        'status'            => 'ACTIVE',
    ];
});
