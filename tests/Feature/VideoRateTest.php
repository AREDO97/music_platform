<?php

use App\Models\User;
use App\Models\Video;
use App\Models\VideoRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can rate or update rating for a video', function () {
    $user = User::factory()->create();
    $video = Video::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson("/api/v1/video_ratings/{$video->id}", [
        'rating' => 4,
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('rating.rating', 4);

    $this->assertDatabaseHas('video_ratings', [
        'user_id'  => $user->id,
        'video_id' => $video->id,
        'rating'   => 4,
    ]);
});

test('authenticated user can view video ratings summary', function () {
    $user = User::factory()->create();
    $video = Video::factory()->create();
    Sanctum::actingAs($user);

    VideoRating::factory()->create([
        'video_id' => $video->id,
        'rating'   => 3,
    ]);
    VideoRating::factory()->create([
        'video_id' => $video->id,
        'rating'   => 5,
    ]);

    $response = $this->getJson("/api/v1/video_ratings/{$video->id}");

    $response->assertStatus(200)
             ->assertJson([
                 'total_rating'   => 8,
                 'total_user'     => 2,
                 'average_rating' => 4.0,
             ]);
});