<?php

namespace Tests\Feature\Skills;

use App\Models\Skill;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_skills_can_be_seeded(): void
    {
        $this->seed(SkillSeeder::class);

        $this->assertDatabaseCount('skills', 10);

        $this->assertDatabaseHas('skills', [
            'code' => 'COM',
            'name' => 'Comunicación',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('skills', [
            'code' => 'EMP',
            'name' => 'Empatía',
            'is_active' => true,
        ]);
    }

    public function test_skill_code_and_name_are_normalized(): void
    {
        $skill = Skill::factory()->create([
            'code' => ' com ',
            'name' => '  Comunicación   efectiva ',
        ]);

        $this->assertSame('COM', $skill->code);
        $this->assertSame(
            'Comunicación efectiva',
            $skill->name,
        );
    }

    public function test_active_scope_only_returns_active_skills(): void
    {
        Skill::factory()->create([
            'name' => 'Habilidad activa',
            'is_active' => true,
        ]);

        Skill::factory()->inactive()->create([
            'name' => 'Habilidad inactiva',
        ]);

        $skills = Skill::query()
            ->active()
            ->get();

        $this->assertCount(1, $skills);
        $this->assertSame(
            'Habilidad activa',
            $skills->first()->name,
        );
    }

    public function test_skill_uses_soft_deletes(): void
    {
        $skill = Skill::factory()->create();

        $skill->delete();

        $this->assertSoftDeleted($skill);

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
        ]);

        $this->assertNull(
            Skill::query()->find($skill->id)
        );

        $this->assertNotNull(
            Skill::withTrashed()->find($skill->id)
        );
    }
}
