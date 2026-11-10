<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'genre_id'         => Genre::factory(),
            'title'            => fake()->sentence(3),
            'description'      => fake()->sentence(),
            'audio_path'       => 'tracks/audio/sample.mp3',
            'image_cover'      => 'tracks/covers/sample.png',
            'duration_seconds' => fake()->numberBetween(120, 360),
            'is_active'        => true,
        ];
    }
}