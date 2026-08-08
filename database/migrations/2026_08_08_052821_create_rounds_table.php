<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('tournament_groups')->nullOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('number');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_knockout')->default(false);
            $table->timestamps();

            $table->unique(['tournament_id', 'group_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
