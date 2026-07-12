<?php

namespace Tests\Feature\Skills;

use App\Enums\RoleName;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_administrator_can_manage_skills(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        $skill = Skill::factory()->create();

        $this->assertTrue(
            $admin->can('viewAny', Skill::class)
        );

        $this->assertTrue(
            $admin->can('view', $skill)
        );

        $this->assertTrue(
            $admin->can('create', Skill::class)
        );

        $this->assertTrue(
            $admin->can('update', $skill)
        );

        $this->assertTrue(
            $admin->can('delete', $skill)
        );

        $this->assertTrue(
            $admin->can('restore', $skill)
        );

        $this->assertFalse(
            $admin->can('forceDelete', $skill)
        );
    }

    public function test_participant_cannot_manage_skills(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $skill = Skill::factory()->create();

        $this->assertFalse(
            $participant->can('viewAny', Skill::class)
        );

        $this->assertFalse(
            $participant->can('view', $skill)
        );

        $this->assertFalse(
            $participant->can('create', Skill::class)
        );

        $this->assertFalse(
            $participant->can('update', $skill)
        );

        $this->assertFalse(
            $participant->can('delete', $skill)
        );

        $this->assertFalse(
            $participant->can('restore', $skill)
        );
    }
}
