<?php

use App\Models\Genre;
use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can upload a track', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $genre = Genre::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/tracks', [
        'title' => 'Test Track Title',
        'genre_id' => $genre->id,
        'description' => 'Test description',
        'audio' => UploadedFile::fake()->create('song.mp3', 1000, 'audio/mpeg'),
        'image_cover' => UploadedFile::fake()->image('cover.jpg'),
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['message', 'track']);

    $this->assertDatabaseHas('tracks', ['title' => 'Test Track Title']);
});

test('authenticated user can view tracks list', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Track::factory()->count(2)->create(['is_active' => true]);

    $response = $this->getJson('/api/v1/tracks');

    $response->assertStatus(200)
             ->assertJsonStructure(['data']);
});

test('track owner can soft delete their track', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $track = Track::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->deleteJson("/api/v1/tracks/{$track->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Track deleted succesifully']);

    $this->assertDatabaseHas('tracks', [
        'id' => $track->id,
        'is_active' => 0,
    ]);
});