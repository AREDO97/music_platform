<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VideoCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'video_category_id' => VideoCategory::factory(),
            'title'             => fake()->sentence(3),
            'description'       => fake()->sentence(),
            'video_path'        => 'videos/files/sample.mp4',
            'image_cover'       => 'videos/covers/sample.jpg',
            'duration_seconds'  => fake()->numberBetween(30, 300),
            'is_active'         => true,
        ];
    }
}