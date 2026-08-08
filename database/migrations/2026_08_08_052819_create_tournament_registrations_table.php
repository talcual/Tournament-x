<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->morphs('participant');
            $table->unsignedTinyInteger('seed')->nullable();
            $table->boolean('is_confirmed')->default(true);
            $table->timestamps();

            $table->unique(['tournament_id', 'participant_id', 'participant_type'], 'tournament_registrations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_registrations');
    }
};
