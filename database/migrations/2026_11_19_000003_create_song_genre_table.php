<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_genre', function (Blueprint $table) {
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gender_id')->constrained('genders')->cascadeOnDelete();

            $table->primary(['song_id', 'gender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_genre');
    }
};
