<?php

namespace Database\Factories;

use App\Models\VideoCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoCategory>
 */
class VideoCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
             'name'=>fake()->name(),
            'description' =>fake()->sentence(),
            'is_active'=>true
        ];
    }
}
