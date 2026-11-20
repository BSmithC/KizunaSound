<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\ListeningHistory;
use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformLibraryTest extends TestCase
{
    use RefreshDatabase;

    private function song(array $attributes = []): Song
    {
        $user = User::factory()->create();
        $genre = Genre::create([
            'name' => 'Rock',
            'description' => 'Género de prueba',
            'slug' => 'rock-'.uniqid(),
            'active' => true,
        ]);

        return Song::create(array_merge([
            'gender_id' => $genre->id,
            'artist_id' => $user->id,
            'name' => 'Canción de prueba',
            'thumbnail' => 'thumbnails/prueba.jpg',
            'lyrics' => 'Letra de prueba',
            'audio' => 'songs/prueba.mp3',
            'is_public' => true,
        ], $attributes));
    }

    public function test_favorite_can_be_toggled(): void
    {
        $user = User::factory()->create();
        $song = $this->song();

        $this->actingAs($user)
            ->post(route('library.favorites.toggle', $song))
            ->assertOk()
            ->assertJson(['favorite' => true]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'song_id' => $song->id,
        ]);

        $this->actingAs($user)
            ->post(route('library.favorites.toggle', $song))
            ->assertOk()
            ->assertJson(['favorite' => false]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'song_id' => $song->id,
        ]);
    }

    public function test_history_is_recorded_and_play_count_increments(): void
    {
        $user = User::factory()->create();
        $song = $this->song();

        $this->actingAs($user)
            ->post(route('library.history.record', $song), ['source' => 'player'])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('listening_history', [
            'user_id' => $user->id,
            'song_id' => $song->id,
            'source' => 'player',
        ]);

        $this->assertSame(1, $song->fresh()->play_count);
    }

    public function test_repeated_history_calls_do_not_double_play_count(): void
    {
        $user = User::factory()->create();
        $song = $this->song();

        $this->actingAs($user)
            ->post(route('library.history.record', $song), ['source' => 'player'])
            ->assertOk();

        $this->actingAs($user)
            ->post(route('library.history.record', $song), ['source' => 'page'])
            ->assertOk();

        $this->assertSame(1, $song->fresh()->play_count);
        $this->assertSame(1, ListeningHistory::where('song_id', $song->id)->count());
    }

    public function test_recent_music_does_not_repeat_songs(): void
    {
        $user = User::factory()->create();
        $first = $this->song(['name' => 'Escuchada dos veces']);
        $second = $this->song(['name' => 'Escuchada después']);

        ListeningHistory::insert([
            [
                'user_id' => $user->id,
                'song_id' => $first->id,
                'listened_at' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
                'source' => 'player',
            ],
            [
                'user_id' => $user->id,
                'song_id' => $second->id,
                'listened_at' => now()->subMinutes(4)->format('Y-m-d H:i:s'),
                'source' => 'player',
            ],
            [
                'user_id' => $user->id,
                'song_id' => $first->id,
                'listened_at' => now()->subMinute()->format('Y-m-d H:i:s'),
                'source' => 'player',
            ],
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Home')
                ->has('recentSongs', 2)
                ->where('recentSongs.0.id', $first->id)
                ->where('recentSongs.1.id', $second->id)
            );

        $this->actingAs($user)
            ->get(route('library'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Library')
                ->has('history', 2)
                ->where('history.0.id', $first->id)
                ->where('history.1.id', $second->id)
            );
    }

    public function test_mood_page_groups_only_real_assigned_moods(): void
    {
        $user = User::factory()->create();
        $withMood = $this->song(['name' => 'Con mood', 'mood' => 'Calma']);
        $withoutMood = $this->song(['name' => 'Sin mood']);

        $this->actingAs($user)
            ->get(route('mood.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Mood')
                ->where('moods.0.mood', 'Calma')
                ->where('moods.0.songs.0.id', $withMood->id)
                ->missing('moods.0.songs.1')
            );

        $this->assertNull($withoutMood->mood);
    }

    public function test_another_user_cannot_modify_a_private_playlist(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $song = $this->song();

        $playlist = Playlist::create([
            'name' => 'Privada',
            'description' => 'Solo del dueño',
            'img' => 'playlists/privada.jpg',
            'user_id' => $owner->id,
            'is_public' => false,
        ]);

        $this->actingAs($intruder)
            ->post(route('playlists.songs.toggle', [$playlist, $song]))
            ->assertForbidden();
    }

    public function test_library_actions_require_authentication(): void
    {
        $song = $this->song();

        $this->post(route('library.favorites.toggle', $song))
            ->assertRedirect(route('login'));
    }
}
