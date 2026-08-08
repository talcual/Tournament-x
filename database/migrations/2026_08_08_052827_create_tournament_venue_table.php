<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_venue', function (Blueprint $table) {
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->primary(['tournament_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_venue');
    }
};
