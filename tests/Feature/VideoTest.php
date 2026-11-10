<?php

use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('authenticated user can upload a video', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = VideoCategory::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/videos', [
        'title' => 'Amazing Video',
        'video_category_id' => $category->id,
        'description' => 'Sample description',
        'video' => UploadedFile::fake()->create('clip.mp4', 2000, 'video/mp4'),
        'image_cover' => UploadedFile::fake()->image('thumbnail.jpg'),
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure(['message', 'video']);

    $this->assertDatabaseHas('videos', ['title' => 'Amazing Video']);
});

test('authenticated user can view active videos', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Video::factory()->count(3)->create(['is_active' => true]);

    $response = $this->getJson('/api/v1/videos');

    $response->assertStatus(200)
             ->assertJsonStructure(['data']);
});

test('video owner can soft delete their video', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $video = Video::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->deleteJson("/api/v1/videos/{$video->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Video deleted successfully']);

    $this->assertDatabaseHas('videos', [
        'id' => $video->id,
        'is_active' => 0,
    ]);
});