<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('artist_ref_id')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('play_count')->default(0);
            $table->string('license')->nullable();
            $table->softDeletes();

            $table->index('name');
            $table->index('gender_id');
            $table->index('artist_ref_id');
            $table->index(['is_public', 'created_at']);
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreignId('artist_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('is_public')->default(true);
            $table->softDeletes();
            $table->string('img')->nullable()->change();

            $table->index('name');
            $table->index('artist_id');
            $table->index('user_id');
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_collaborative')->default(false);
            $table->softDeletes();
            $table->string('img')->nullable()->change();

            $table->index('user_id');
            $table->index(['is_public', 'created_at']);
        });

        Schema::table('genders', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->boolean('active')->default(true);
            $table->softDeletes();

            $table->index('name');
            $table->unique('slug');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('song_id')->nullable()->change();
            $table->foreignId('album_id')->nullable()->change();
            $table->foreignId('playlist_id')->nullable()->change();
            $table->json('metadata')->nullable();

            $table->index(['type', 'created_at']);
            $table->index('user_id');
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('credit_details', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('song_id');
        });
    }

    public function down(): void
    {
        Schema::table('credit_details', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['song_id']);
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['type', 'created_at']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('metadata');
        });

        Schema::table('genders', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['name']);
            $table->dropSoftDeletes();
            $table->dropColumn(['slug', 'active']);
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['is_public', 'created_at']);
            $table->dropSoftDeletes();
            $table->dropColumn(['user_id', 'is_public', 'is_collaborative']);
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['artist_id']);
            $table->dropIndex(['user_id']);
            $table->dropSoftDeletes();
            $table->dropColumn(['artist_id', 'user_id', 'release_date', 'is_public']);
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['gender_id']);
            $table->dropIndex(['artist_ref_id']);
            $table->dropIndex(['is_public', 'created_at']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'artist_ref_id',
                'duration_seconds',
                'release_date',
                'is_public',
                'play_count',
                'license',
            ]);
        });
    }
};
