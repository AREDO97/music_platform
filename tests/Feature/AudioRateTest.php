<?php

use App\Models\AudioRating;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can rate or update rating for a track', function () {
    $user = User::factory()->create();
    $track = Track::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson("/api/v1/audio_ratings/{$track->id}", [
        'rating' => 5,
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('rating', 5); // Changed from 'rating.rating' to 'rating'

    $this->assertDatabaseHas('audio_ratings', [
        'user_id'  => $user->id,
        'track_id' => $track->id,
        'rating'   => 5,
    ]);
});

test('authenticated user can view audio ratings summary', function () {
    $user = User::factory()->create();
    $track = Track::factory()->create();
    Sanctum::actingAs($user);

    AudioRating::factory()->create([
        'track_id' => $track->id,
        'rating'   => 4,
    ]);
    AudioRating::factory()->create([
        'track_id' => $track->id,
        'rating'   => 5,
    ]);

    $response = $this->getJson("/api/v1/audio_ratings/{$track->id}");

    $response->assertStatus(200)
             ->assertJson([
                 'total_rating'   => 9,
                 'total_user'     => 2,
                 'average_rating' => 4.5,
             ]);
});