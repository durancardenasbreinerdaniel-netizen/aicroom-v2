<?php

namespace Tests\Feature\Authorization;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')
            ->assertRedirect();
    }

    public function test_participant_cannot_access_admin_panel(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $this
            ->actingAs($participant)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_administrator_can_access_admin_panel(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        $this
            ->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_inactive_admin_cannot_access_admin_panel(): void
    {
        $admin = User::factory()
            ->inactive()
            ->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        $this
            ->actingAs($admin)
            ->get('/admin')
            ->assertForbidden();
    }
}
