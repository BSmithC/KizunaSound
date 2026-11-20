<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('mood')->nullable()->after('license');
            $table->index('mood');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreignId('song_id')->nullable()->change();
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->foreignId('song_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->foreignId('song_id')->nullable(false)->change();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreignId('song_id')->nullable(false)->change();
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex(['mood']);
            $table->dropColumn('mood');
        });
    }
};
