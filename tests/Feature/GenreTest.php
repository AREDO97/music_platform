<?php

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('admin or super_admin can create a genre', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/v1/genres', [
        'name' => 'Afrobeats',
        'description' => 'Vibrant African rhythms',
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Genre created successifully']);

    $this->assertDatabaseHas('genres', ['name' => 'Afrobeats']);
});

test('standard member cannot create a genre', function () {
    $member = User::factory()->create(['role' => 'user']);
    Sanctum::actingAs($member);

    $response = $this->postJson('/api/v1/genres', [
        'name' => 'Rock',
    ]);

    $response->assertStatus(403);
});

test('authenticated user can view active genres', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Genre::factory()->count(3)->create(['is_active' => true]);

    $response = $this->getJson('/api/v1/genres');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

test('admin can update a genre', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    $genre = Genre::factory()->create(['name' => 'Old Name']);

    $response = $this->patchJson("/api/v1/genres/{$genre->id}", [
        'name' => 'New Name',
    ]);

    $response->assertStatus(200)
             ->assertJson(['name' => 'New Name']);
});

test('admin can soft delete a genre', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($admin);

    $genre = Genre::factory()->create(['is_active' => true]);

    $response = $this->deleteJson("/api/v1/genres/{$genre->id}");

    $response->assertStatus(200);
    $this->assertDatabaseHas('genres', [
        'id' => $genre->id,
        'is_active' => 0,
    ]);
});