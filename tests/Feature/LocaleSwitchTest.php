<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_set(): void
    {
        $this->assertSame('es', app()->getLocale());
    }

    public function test_locale_switch_route_changes_locale(): void
    {
        $response = $this->post(route('locale.switch'), ['locale' => 'en']);
        $response->assertSessionHas('locale', 'en');
    }

    public function test_locale_switch_rejects_invalid_locale(): void
    {
        $response = $this->post(route('locale.switch'), ['locale' => 'klingon']);
        $response->assertSessionHas('locale', config('app.locale'));
    }

    public function test_homepage_renders_in_spanish_by_default(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk()->assertSee(__('app.public.discover_title'));
    }
}
