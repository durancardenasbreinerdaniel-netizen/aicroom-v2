<?php

namespace Tests\Feature\Skills;

use App\Enums\RoleName;
use App\Filament\Resources\Skills\SkillResource;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_administrator_can_access_skill_resource(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        Skill::factory()->create([
            'code' => 'COM',
            'name' => 'Comunicación',
        ]);

        $this
            ->actingAs($admin)
            ->get(
                SkillResource::getUrl(
                    'index',
                    panel: 'admin',
                )
            )
            ->assertOk()
            ->assertSee('Comunicación');
    }

    public function test_participant_cannot_access_skill_resource(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $this
            ->actingAs($participant)
            ->get('/admin/skills')
            ->assertForbidden();
    }
}
