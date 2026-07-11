<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_availability_check_reports_taken_username_and_email(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Jamie',
            'last_name' => 'Doe',
            'username' => 'jamiedoe',
            'email' => 'jamie@example.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->getJson('/check-availability?field=username&value=jamiedoe')
            ->assertOk()
            ->assertExactJson(['taken' => true]);

        $this->getJson('/check-availability?field=email&value=available@example.com')
            ->assertOk()
            ->assertExactJson(['taken' => false]);
    }

    public function test_availability_check_ignores_unsupported_or_empty_fields(): void
    {
        $this->getJson('/check-availability?field=role&value=admin')
            ->assertOk()
            ->assertExactJson(['taken' => false]);

        $this->getJson('/check-availability?field=email')
            ->assertOk()
            ->assertExactJson(['taken' => false]);
    }
}
