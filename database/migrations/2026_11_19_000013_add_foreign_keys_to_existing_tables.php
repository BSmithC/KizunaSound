<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->foreign('gender_id')->references('id')->on('genders')->restrictOnDelete();
            $table->foreign('artist_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('artist_ref_id')->references('id')->on('artists')->nullOnDelete();
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->foreign('credit_details_id')->references('id')->on('credit_details')->cascadeOnDelete();
        });

        Schema::table('credit_details', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('song_id')->references('id')->on('songs')->cascadeOnDelete();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreign('song_id')->references('id')->on('songs')->restrictOnDelete();
            $table->foreign('artist_id')->references('id')->on('artists')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->foreign('song_id')->references('id')->on('songs')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('song_id')->references('id')->on('songs')->nullOnDelete();
            $table->foreign('album_id')->references('id')->on('albums')->nullOnDelete();
            $table->foreign('playlist_id')->references('id')->on('playlists')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['song_id']);
            $table->dropForeign(['album_id']);
            $table->dropForeign(['playlist_id']);
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->dropForeign(['song_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropForeign(['song_id']);
            $table->dropForeign(['artist_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('credit_details', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['song_id']);
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->dropForeign(['credit_details_id']);
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropForeign(['artist_id']);
            $table->dropForeign(['artist_ref_id']);
        });
    }
};
