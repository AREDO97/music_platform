<?php

use App\Models\User;
use App\Models\Video;
use App\Models\WatchList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can view their watchlist', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    WatchList::factory()->count(2)->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/v1/watchlist');

    $response->assertStatus(200)
             ->assertJsonCount(2);
});

test('authenticated user can add a video to watchlist', function () {
    $user = User::factory()->create();
    $video = Video::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson("/api/v1/watchlist/{$video->id}");

    $response->assertStatus(201)
             ->assertJson(['message' => 'Video added to watchlist']);

    $this->assertDatabaseHas('watch_lists', [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ]);
});

test('authenticated user can remove a video from watchlist', function () {
    $user = User::factory()->create();
    $video = Video::factory()->create();
    Sanctum::actingAs($user);

    WatchList::factory()->create([
        'user_id' => $user->id,
        'video_id' => $video->id,
    ]);

    $response = $this->deleteJson("/api/v1/watchlist/{$video->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Video removed from watchlist']);

    $this->assertDatabaseMissing('watch_lists', [
        'user_id' => $user->id,
        'video_id' => $video->id,
    ]);
});