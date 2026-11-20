<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Los artistas existentes hoy son usuarios (songs.artist_id -> users.id).
        // Se crea su perfil de artista y se conserva la columna original intacta.
        $userIds = DB::table('songs')->whereNotNull('artist_id')->pluck('artist_id')->unique();

        foreach ($userIds as $userId) {
            $user = DB::table('users')->find($userId);

            if (! $user) {
                continue;
            }

            $base = Str::slug($user->name) ?: 'artista';
            $slug = $base;
            $suffix = 1;

            while (DB::table('artists')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            $artistId = DB::table('artists')->insertGetId([
                'user_id' => $user->id,
                'name' => $user->name,
                'slug' => $slug,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('songs')
                ->where('artist_id', $user->id)
                ->whereNull('artist_ref_id')
                ->update(['artist_ref_id' => $artistId]);
        }

        foreach (DB::table('songs')->select('id', 'gender_id')->get() as $song) {
            DB::table('song_genre')->insertOrIgnore([
                'song_id' => $song->id,
                'gender_id' => $song->gender_id,
            ]);
        }

        foreach (DB::table('albums')->select('id', 'song_id')->get() as $album) {
            DB::table('album_song')->insertOrIgnore([
                'album_id' => $album->id,
                'song_id' => $album->song_id,
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (DB::table('playlists')->select('id', 'song_id')->get() as $playlist) {
            DB::table('playlist_song')->insertOrIgnore([
                'playlist_id' => $playlist->id,
                'song_id' => $playlist->song_id,
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (DB::table('genders')->get() as $gender) {
            DB::table('genders')->where('id', $gender->id)->update([
                'slug' => Str::slug($gender->name).'-'.$gender->id,
                'active' => true,
            ]);
        }

        foreach (['admin', 'editor', 'listener'] as $role) {
            DB::table('roles')->insertOrIgnore([
                'name' => ucfirst($role),
                'slug' => $role,
                'description' => 'Rol del sistema KizunaSound',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (DB::table('users')->pluck('id') as $userId) {
            DB::table('user_settings')->insertOrIgnore([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Los datos backfiteados se conservan: las migraciones estructurales
        // posteriores se encargan de eliminar las columnas/tablas al revertir.
    }
};
