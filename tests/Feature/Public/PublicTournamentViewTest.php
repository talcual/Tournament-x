<?php

namespace Tests\Feature\Public;

use App\Models\Sport;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTournamentViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible_to_guests(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_home_lists_published_tournaments(): void
    {
        $sport = Sport::factory()->create();
        $organizer = User::factory()->create();

        $open = Tournament::factory()->for($sport, 'sport')->for($organizer, 'organizer')->registration()->create();
        $draft = Tournament::factory()->for($sport, 'sport')->for($organizer, 'organizer')->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($open->name)
            ->assertDontSee($draft->name);
    }

    public function test_guest_can_view_a_tournament_detail(): void
    {
        $tournament = Tournament::factory()->for(Sport::factory(), 'sport')->for(User::factory(), 'organizer')->registration()->create();

        $this->get(route('public.tournaments.show', $tournament))
            ->assertOk()
            ->assertSee($tournament->name);
    }
}
