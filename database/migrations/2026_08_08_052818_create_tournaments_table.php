<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('format');
            $table->string('status')->default('draft');
            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedTinyInteger('min_participants')->default(2);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamp('registration_deadline')->nullable();
            $table->string('participant_type')->default('team');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
