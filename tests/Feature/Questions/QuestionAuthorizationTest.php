<?php

namespace Tests\Feature\Questions;

use App\Enums\RoleName;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_administrator_can_manage_questions(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        $question = Question::factory()->create();

        $this->assertTrue(
            $admin->can('viewAny', Question::class)
        );

        $this->assertTrue(
            $admin->can('view', $question)
        );

        $this->assertTrue(
            $admin->can('create', Question::class)
        );

        $this->assertTrue(
            $admin->can('update', $question)
        );

        $this->assertTrue(
            $admin->can('delete', $question)
        );

        $this->assertTrue(
            $admin->can('restore', $question)
        );

        $this->assertFalse(
            $admin->can('forceDelete', $question)
        );
    }

    public function test_participant_cannot_manage_questions(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $question = Question::factory()->create();

        $this->assertFalse(
            $participant->can('viewAny', Question::class)
        );

        $this->assertFalse(
            $participant->can('view', $question)
        );

        $this->assertFalse(
            $participant->can('create', Question::class)
        );

        $this->assertFalse(
            $participant->can('update', $question)
        );

        $this->assertFalse(
            $participant->can('delete', $question)
        );

        $this->assertFalse(
            $participant->can('restore', $question)
        );
    }
}
