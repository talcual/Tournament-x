<?php

namespace Tests\Feature\Admin;

use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_venue(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->post(route('admin.venues.store'), [
            'name' => 'Stadium X',
            'address' => '1 Main',
            'city' => 'Madrid',
            'country' => 'Spain',
            'capacity' => 50000,
        ])->assertRedirect(route('admin.venues.index'));

        $this->assertDatabaseHas('venues', ['name' => 'Stadium X', 'city' => 'Madrid']);
    }

    public function test_admin_can_update_venue(): void
    {
        $admin = $this->createUserWithRole('admin');
        $venue = Venue::factory()->create();

        $this->actingAs($admin)->put(route('admin.venues.update', $venue), [
            'name' => 'Renamed',
            'address' => $venue->address,
            'city' => $venue->city,
            'country' => $venue->country,
        ])->assertRedirect(route('admin.venues.index'));

        $this->assertDatabaseHas('venues', ['id' => $venue->id, 'name' => 'Renamed']);
    }

    public function test_admin_can_delete_venue(): void
    {
        $admin = $this->createUserWithRole('admin');
        $venue = Venue::factory()->create();

        $this->actingAs($admin)->delete(route('admin.venues.destroy', $venue))->assertRedirect(route('admin.venues.index'));

        $this->assertDatabaseMissing('venues', ['id' => $venue->id]);
    }
}
