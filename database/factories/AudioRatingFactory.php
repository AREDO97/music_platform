<?php

namespace Database\Factories;

use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AudioRatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'track_id' => Track::factory(),
            'rating'   => fake()->numberBetween(1, 5),
        ];
    }
}