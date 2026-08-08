<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('tournament_groups')->nullOnDelete();
            $table->morphs('participant');
            $table->unsignedTinyInteger('played')->default(0);
            $table->unsignedTinyInteger('wins')->default(0);
            $table->unsignedTinyInteger('draws')->default(0);
            $table->unsignedTinyInteger('losses')->default(0);
            $table->unsignedSmallInteger('goals_for')->default(0);
            $table->unsignedSmallInteger('goals_against')->default(0);
            $table->unsignedSmallInteger('points')->default(0);
            $table->unsignedTinyInteger('position')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'group_id', 'participant_id', 'participant_type'], 'standings_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
