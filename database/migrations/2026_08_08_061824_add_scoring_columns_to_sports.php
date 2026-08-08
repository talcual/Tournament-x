<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sports', function (Blueprint $table): void {
            $table->unsignedTinyInteger('points_per_win')->default(3)->after('is_team_sport');
            $table->unsignedTinyInteger('points_per_draw')->default(1)->after('points_per_win');
            $table->unsignedTinyInteger('points_per_loss')->default(0)->after('points_per_draw');
            $table->boolean('allows_draws')->default(true)->after('points_per_loss');
        });
    }

    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table): void {
            $table->dropColumn(['points_per_win', 'points_per_draw', 'points_per_loss', 'allows_draws']);
        });
    }
};
