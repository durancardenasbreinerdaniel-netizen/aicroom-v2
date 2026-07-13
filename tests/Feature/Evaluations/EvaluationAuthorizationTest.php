<?php

namespace Tests\Feature\Evaluations;

use App\Enums\RoleName;
use App\Models\Evaluation;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(
            RoleAndPermissionSeeder::class
        );
    }

    public function test_participant_can_create_evaluation(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $this->assertTrue(
            $participant->can(
                'create',
                Evaluation::class
            )
        );
    }

    public function test_participant_can_view_own_evaluation(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $evaluation = Evaluation::factory()
            ->for($participant)
            ->create();

        $this->assertTrue(
            $participant->can(
                'view',
                $evaluation
            )
        );
    }

    public function test_participant_cannot_view_another_users_evaluation(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $evaluation = Evaluation::factory()
            ->create();

        $this->assertFalse(
            $participant->can(
                'view',
                $evaluation
            )
        );
    }

    public function test_administrator_can_view_any_evaluation(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        $evaluation = Evaluation::factory()
            ->create();

        $this->assertTrue(
            $admin->can(
                'view',
                $evaluation
            )
        );

        $this->assertTrue(
            $admin->can(
                'viewAny',
                Evaluation::class
            )
        );
    }
}
