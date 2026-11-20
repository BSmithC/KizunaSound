<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use App\Services\CatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogServiceTest extends TestCase
{
    use RefreshDatabase;

    private function catalog(): CatalogService
    {
        return app(CatalogService::class);
    }

    private function song(User $uploader, Genre $genre, array $attributes = []): Song
    {
        return Song::create(array_merge([
            'gender_id' => $genre->id,
            'artist_id' => $uploader->id,
            'name' => 'Canción de prueba',
            'thumbnail' => 'thumbnails/prueba.jpg',
            'lyrics' => 'Letra de prueba',
            'audio' => 'songs/prueba.mp3',
            'is_public' => true,
        ], $attributes));
    }

    public function test_explore_filters_by_genre_slug(): void
    {
        $uploader = User::factory()->create();
        $rock = Genre::create(['name' => 'Rock', 'description' => 'Rock', 'slug' => 'rock', 'active' => true]);
        $jazz = Genre::create(['name' => 'Jazz', 'description' => 'Jazz', 'slug' => 'jazz', 'active' => true]);

        $this->song($uploader, $rock, ['name' => 'Canción de rock']);
        $this->song($uploader, $jazz, ['name' => 'Canción de jazz']);

        $result = $this->catalog()->explore('rock');

        $this->assertSame('Rock', $result['activeGenre']->name);
        $this->assertCount(1, $result['songs']);
        $this->assertSame('Canción de rock', $result['songs']->first()->name);
    }

    public function test_search_only_returns_public_songs(): void
    {
        $uploader = User::factory()->create();
        $genre = Genre::create(['name' => 'Pop', 'description' => 'Pop', 'slug' => 'pop', 'active' => true]);

        $this->song($uploader, $genre, ['name' => 'Katelyn publicada', 'is_public' => true]);
        $this->song($uploader, $genre, ['name' => 'Katelyn privada', 'is_public' => false]);

        $result = $this->catalog()->search('Katelyn', $uploader);

        $this->assertCount(1, $result['songs']);
        $this->assertSame('Katelyn publicada', $result['songs']->first()->name);
    }

    public function test_home_payload_includes_real_catalog_sections(): void
    {
        $user = User::factory()->create();
        $genre = Genre::create(['name' => 'Pop', 'description' => 'Pop', 'slug' => 'pop', 'active' => true]);
        $this->song($user, $genre, ['name' => 'Éxito real', 'mood' => 'Calma']);

        $payload = $this->catalog()->homePayload($user);

        $this->assertArrayHasKey('recommendedSongs', $payload);
        $this->assertArrayHasKey('moods', $payload);
        $this->assertSame('Calma', $payload['moods']->first()['mood']);
    }
}
