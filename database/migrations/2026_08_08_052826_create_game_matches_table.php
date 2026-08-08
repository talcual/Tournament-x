<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('round_id')->nullable()->constrained('rounds')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('tournament_groups')->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('round_number')->default(1);
            $table->unsignedTinyInteger('match_number')->default(1);
            $table->string('bracket_side', 20)->nullable();
            $table->unsignedTinyInteger('bracket_index')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('next_match_id')->nullable()->constrained('game_matches')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
