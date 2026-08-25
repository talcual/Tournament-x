<?php

namespace Tests\Feature\Authorization;

use App\Models\Sport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_plain_user_is_forbidden_from_admin(): void
    {
        $user = $this->createUserWithRole('user');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_organizer_can_access_dashboard(): void
    {
        $organizer = $this->createUserWithRole('organizer');

        $this->actingAs($organizer)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_user_role_can_create_tournaments(): void
    {
        $user = $this->createUserWithRole('user');
        $sport = Sport::factory()->create();

        $this->actingAs($user)
            ->post(route('user.tournaments.store'), [
                'sport_id' => $sport->id,
                'organizer_id' => $user->id,
                'name' => 'My Community Cup',
                'description' => 'Community tournament created by a regular user.',
                'format' => 'single_elimination',
                'status' => 'registration',
                'participant_type' => 'team',
                'min_participants' => 4,
                'max_participants' => 16,
                'starts_at' => now()->addMonth()->format('Y-m-d'),
                'ends_at' => now()->addMonths(2)->format('Y-m-d'),
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tournaments', ['name' => 'My Community Cup', 'organizer_id' => $user->id]);
    }

    public function test_organizer_can_create_tournament(): void
    {
        $organizer = $this->createUserWithRole('organizer');
        $sport = Sport::factory()->create();

        $this->actingAs($organizer)
            ->get(route('admin.tournaments.create'))
            ->assertOk();
    }

    public function test_only_super_admin_manages_users(): void
    {
        $admin = $this->createUserWithRole('admin');
        $super = $this->createUserWithRole('super-admin');

        $this->actingAs($admin)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($super)->get(route('admin.users.index'))->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
}
