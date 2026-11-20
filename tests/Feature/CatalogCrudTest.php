<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function genre(): Genre
    {
        return Genre::create([
            'name' => 'Rock',
            'description' => 'Género de prueba',
            'slug' => 'rock',
            'active' => true,
        ]);
    }

    private function song(User $user, Genre $genre, array $attributes = []): Song
    {
        return Song::create(array_merge([
            'gender_id' => $genre->id,
            'artist_id' => $user->id,
            'name' => 'Canción original',
            'thumbnail' => '',
            'lyrics' => 'Letra original',
            'audio' => 'songs/original.mp3',
            'is_public' => true,
        ], $attributes));
    }

    public function test_song_can_be_created_with_real_catalog_data(): void
    {
        $user = User::factory()->create();
        $genre = $this->genre();

        $response = $this->actingAs($user)->post(route('songs.store'), [
            'name' => 'Canción nueva',
            'gender_id' => $genre->id,
            'lyrics' => 'Letra nueva',
            'audio' => UploadedFile::fake()->create('nueva.mp3', 128, 'audio/mpeg'),
            'mood' => 'Calma',
            'is_public' => true,
        ]);

        $response->assertRedirect(route('songs.index'));
        $this->assertDatabaseHas('songs', [
            'name' => 'Canción nueva',
            'gender_id' => $genre->id,
            'artist_id' => $user->id,
            'mood' => 'Calma',
        ]);
    }

    public function test_song_can_be_updated_without_uploading_new_audio(): void
    {
        $user = User::factory()->create();
        $genre = $this->genre();
        $song = $this->song($user, $genre);

        $response = $this->actingAs($user)->put(route('songs.update', $song), [
            'name' => 'Canción editada',
            'gender_id' => $genre->id,
            'lyrics' => 'Letra editada',
            'mood' => 'Euforia',
            'is_public' => true,
        ]);

        $response->assertRedirect(route('songs.show', $song));
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'name' => 'Canción editada',
            'mood' => 'Euforia',
            'audio' => 'songs/original.mp3',
        ]);
    }

    public function test_album_syncs_selected_real_songs(): void
    {
        $user = User::factory()->create();
        $genre = $this->genre();
        $first = $this->song($user, $genre, ['name' => 'Primera']);
        $second = $this->song($user, $genre, ['name' => 'Segunda']);

        $response = $this->actingAs($user)->post(route('albums.store'), [
            'name' => 'Álbum de prueba',
            'license' => 'CC BY',
            'songs' => [$first->id, $second->id],
            'is_public' => true,
        ]);

        $album = Album::where('name', 'Álbum de prueba')->firstOrFail();
        $response->assertRedirect(route('albums.show', $album));

        $this->assertDatabaseHas('album_song', [
            'album_id' => $album->id,
            'song_id' => $first->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('album_song', [
            'album_id' => $album->id,
            'song_id' => $second->id,
            'position' => 2,
        ]);
        $this->assertSame($user->id, $album->user_id);
    }
}
