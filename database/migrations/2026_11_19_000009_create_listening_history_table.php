<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listening_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->timestamp('listened_at')->useCurrent();
            $table->string('source')->nullable();

            $table->index(['user_id', 'listened_at']);
            $table->index('song_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listening_history');
    }
};
