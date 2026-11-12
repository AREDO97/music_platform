<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoRating>
 */
class VideoRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'video_id' => Video::factory(),
            'rating'   => fake()->numberBetween(1, 5),
        ];
    }
}
