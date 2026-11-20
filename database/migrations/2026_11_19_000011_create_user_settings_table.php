<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('theme')->default('dark');
            $table->string('accent_color')->default('#E11D48');
            $table->string('playback_quality')->default('auto');
            $table->boolean('autoplay')->default(true);
            $table->boolean('normalize_volume')->default(false);
            $table->decimal('volume', 3, 2)->default(0.70);
            $table->boolean('shuffle')->default(false);
            $table->string('repeat')->default('off');
            $table->string('language')->default('es');
            $table->string('privacy')->default('private');
            $table->boolean('notifications')->default(true);
            $table->json('audio_preferences')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
