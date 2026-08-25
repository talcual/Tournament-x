<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DatabaseSeederIdempotenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_can_run_twice_without_unique_constraint_errors(): void
    {
        Artisan::call('db:seed', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);

        $this->assertTrue(true);
    }
}
