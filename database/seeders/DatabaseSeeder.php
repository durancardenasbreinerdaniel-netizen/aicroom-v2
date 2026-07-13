<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta los seeders principales de AICROOM.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            SkillSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
