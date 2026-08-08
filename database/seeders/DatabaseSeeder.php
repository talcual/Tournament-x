<?php

namespace Database\Seeders;

use App\Enums\TournamentFormat;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $football = Sport::firstOrCreate(['slug' => 'football'], [
            'name' => 'Football',
            'icon' => '⚽',
            'description' => 'Association football.',
            'is_team_sport' => true,
            'points_per_win' => 3,
            'points_per_draw' => 1,
            'points_per_loss' => 0,
            'allows_draws' => true,
        ]);

        $basketball = Sport::firstOrCreate(['slug' => 'basketball'], [
            'name' => 'Basketball',
            'icon' => '🏀',
            'description' => '5v5 basketball competition.',
            'is_team_sport' => true,
            'points_per_win' => 2,
            'points_per_draw' => 0,
            'points_per_loss' => 0,
            'allows_draws' => false,
        ]);

        $volleyball = Sport::firstOrCreate(['slug' => 'volleyball'], [
            'name' => 'Volleyball',
            'icon' => '🏐',
            'description' => 'Best of 5 sets volleyball.',
            'is_team_sport' => true,
            'points_per_win' => 1,
            'points_per_draw' => 0,
            'points_per_loss' => 0,
            'allows_draws' => false,
        ]);

        $tennis = Sport::firstOrCreate(['slug' => 'tennis'], [
            'name' => 'Tennis',
            'icon' => '🎾',
            'description' => 'Singles tennis tournaments.',
            'is_team_sport' => false,
            'points_per_win' => 1,
            'points_per_draw' => 0,
            'points_per_loss' => 0,
            'allows_draws' => false,
        ]);

        $chess = Sport::firstOrCreate(['slug' => 'chess'], [
            'name' => 'Chess',
            'icon' => '♟️',
            'description' => 'Classical and rapid chess events.',
            'is_team_sport' => false,
            'points_per_win' => 1,
            'points_per_draw' => 0.5,
            'points_per_loss' => 0,
            'allows_draws' => true,
        ]);

        $venues = collect([
            ['name' => 'North Arena', 'address' => '12 Main St', 'city' => 'Barcelona', 'country' => 'Spain', 'capacity' => 55000],
            ['name' => 'Olympic Stadium', 'address' => '500 Olympic Way', 'city' => 'Madrid', 'country' => 'Spain', 'capacity' => 72000],
            ['name' => 'Downtown Court', 'address' => '88 Plaza Rd', 'city' => 'Valencia', 'country' => 'Spain', 'capacity' => 3500],
            ['name' => 'Coastal Pavilion', 'address' => '4 Seaside Ave', 'city' => 'Malaga', 'country' => 'Spain', 'capacity' => 12000],
        ])->map(fn ($v) => Venue::firstOrCreate(['name' => $v['name']], $v));

        $organizer = User::where('email', 'organizer@tournament-x.test')->first();
        $admin = User::where('email', 'admin@tournament-x.test')->first();

        $footballTeams = Team::factory()->count(8)->create(['sport_id' => $football->id]);
        $basketballTeams = Team::factory()->count(6)->create(['sport_id' => $basketball->id]);

        $tennisPlayers = Player::factory()->count(8)->independent()->create(['sport_id' => $tennis->id]);
        $chessPlayers = Player::factory()->count(8)->independent()->create(['sport_id' => $chess->id]);

        $championsLeague = Tournament::factory()
            ->for($football, 'sport')
            ->for($organizer ?? $admin, 'organizer')
            ->registration()
            ->create([
                'name' => 'Champions Cup 2026',
                'slug' => 'champions-cup-2026',
                'description' => 'Annual knockout tournament for top football clubs.',
                'format' => TournamentFormat::SingleElimination,
                'max_participants' => 16,
                'min_participants' => 4,
                'starts_at' => now()->addMonths(1),
                'ends_at' => now()->addMonths(2),
                'registration_deadline' => now()->addWeeks(2),
                'is_featured' => true,
            ]);
        $championsLeague->venues()->sync($venues->first()->id === null ? [] : [$venues->first()->id => ['is_primary' => true]]);
        $championsLeague->venues()->syncWithoutDetaching([$venues[1]->id => ['is_primary' => false]]);

        $leagueSeason = Tournament::factory()
            ->for($basketball, 'sport')
            ->for($organizer ?? $admin, 'organizer')
            ->inProgress()
            ->create([
                'name' => 'City League 2026',
                'slug' => 'city-league-2026',
                'description' => 'Round-robin season for city basketball teams.',
                'format' => TournamentFormat::RoundRobin,
                'max_participants' => 10,
                'min_participants' => 4,
                'starts_at' => now()->subWeeks(2),
                'ends_at' => now()->addWeeks(4),
                'registration_deadline' => now()->subMonths(1),
                'is_featured' => true,
            ]);

        $openTennis = Tournament::factory()
            ->for($tennis, 'sport')
            ->for($organizer ?? $admin, 'organizer')
            ->individual()
            ->registration()
            ->create([
                'name' => 'Open Tennis Series',
                'slug' => 'open-tennis-series',
                'description' => 'Singles knockout tournament with 8 players.',
                'format' => TournamentFormat::SingleElimination,
                'max_participants' => 8,
                'min_participants' => 4,
                'starts_at' => now()->addWeeks(3),
                'ends_at' => now()->addMonths(2),
                'registration_deadline' => now()->addWeeks(1),
                'is_featured' => false,
            ]);

        $chessClassic = Tournament::factory()
            ->for($chess, 'sport')
            ->for($organizer ?? $admin, 'organizer')
            ->individual()
            ->completed()
            ->create([
                'name' => 'Chess Classic 2025',
                'slug' => 'chess-classic-2025',
                'description' => 'Closed swiss-system tournament, classical time control.',
                'format' => TournamentFormat::Swiss,
                'max_participants' => 10,
                'min_participants' => 4,
                'starts_at' => now()->subMonths(3),
                'ends_at' => now()->subMonths(2),
                'registration_deadline' => now()->subMonths(4),
                'is_featured' => false,
            ]);

        $footballTeams->take(6)->each(function (Team $team) use ($championsLeague): void {
            $championsLeague->registrations()->create([
                'participant_id' => $team->id,
                'participant_type' => (new Team)->getMorphClass(),
                'seed' => null,
                'is_confirmed' => true,
            ]);
        });

        $basketballTeams->each(function (Team $team) use ($leagueSeason): void {
            $leagueSeason->registrations()->create([
                'participant_id' => $team->id,
                'participant_type' => (new Team)->getMorphClass(),
                'seed' => null,
                'is_confirmed' => true,
            ]);
        });

        $tennisPlayers->each(function (Player $player) use ($openTennis): void {
            $openTennis->registrations()->create([
                'participant_id' => $player->id,
                'participant_type' => (new Player)->getMorphClass(),
                'seed' => null,
                'is_confirmed' => true,
            ]);
        });

        $chessPlayers->each(function (Player $player) use ($chessClassic): void {
            $chessClassic->registrations()->create([
                'participant_id' => $player->id,
                'participant_type' => (new Player)->getMorphClass(),
                'seed' => null,
                'is_confirmed' => true,
            ]);
        });

        DB::table('tournament_groups')->insert([
            ['tournament_id' => $leagueSeason->id, 'name' => 'Group A', 'code' => 'A', 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['tournament_id' => $leagueSeason->id, 'name' => 'Group B', 'code' => 'B', 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
