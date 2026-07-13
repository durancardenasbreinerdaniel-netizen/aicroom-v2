<?php

namespace Tests\Feature\Questions;

use App\Enums\RoleName;
use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_administrator_can_access_question_resource(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole(
            RoleName::ADMIN->value
        );

        Question::factory()->create([
            'statement' => 'Expreso mis ideas con claridad durante una conversación.',
        ]);

        $this
            ->actingAs($admin)
            ->get(
                QuestionResource::getUrl(
                    'index',
                    panel: 'admin',
                )
            )
            ->assertOk()
            ->assertSee(
                'Expreso mis ideas con claridad durante una conversación.'
            );
    }

    public function test_participant_cannot_access_question_resource(): void
    {
        $participant = User::factory()->create();

        $participant->assignRole(
            RoleName::PARTICIPANT->value
        );

        $this
            ->actingAs($participant)
            ->get('/admin/questions')
            ->assertForbidden();
    }
}
