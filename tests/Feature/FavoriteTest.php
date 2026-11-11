<?php

use App\Models\Favorite;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can view their favorites', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Favorite::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/v1/favorites');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

test('authenticated user can add a track to favorites', function () {
    $user = User::factory()->create();
    $track = Track::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson("/api/v1/favorites/{$track->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Track added to favorites']);

    $this->assertDatabaseHas('favorites', [
        'user_id' => $user->id,
        'track_id' => $track->id,
    ]);
});

test('authenticated user can remove a track from favorites', function () {
    $user = User::factory()->create();
    $track = Track::factory()->create();
    Sanctum::actingAs($user);

    Favorite::factory()->create([
        'user_id' => $user->id,
        'track_id' => $track->id,
    ]);

    $response = $this->deleteJson("/api/v1/favorites/{$track->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Track removed from favorites']);

    $this->assertDatabaseMissing('favorites', [
        'user_id' => $user->id,
        'track_id' => $track->id,
    ]);
});