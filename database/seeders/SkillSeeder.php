<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Registra las habilidades blandas iniciales.
     */
    public function run(): void
    {
        $skills = [
            [
                'code' => 'LEA',
                'name' => 'Liderazgo',
                'description' => 'Capacidad para orientar, motivar y acompañar a otras personas hacia el cumplimiento de objetivos.',
            ],
            [
                'code' => 'COM',
                'name' => 'Comunicación',
                'description' => 'Capacidad para expresar ideas con claridad, escuchar activamente y adaptar el mensaje al contexto.',
            ],
            [
                'code' => 'TEA',
                'name' => 'Trabajo en equipo',
                'description' => 'Capacidad para colaborar, aportar y construir resultados junto con otras personas.',
            ],
            [
                'code' => 'ADA',
                'name' => 'Adaptabilidad',
                'description' => 'Capacidad para responder de manera flexible ante cambios, dificultades y nuevos entornos.',
            ],
            [
                'code' => 'RES',
                'name' => 'Responsabilidad',
                'description' => 'Capacidad para asumir compromisos, cumplir obligaciones y responder por las propias decisiones.',
            ],
            [
                'code' => 'EST',
                'name' => 'Manejo del estrés',
                'description' => 'Capacidad para conservar el control y actuar adecuadamente ante situaciones de presión.',
            ],
            [
                'code' => 'EMP',
                'name' => 'Empatía',
                'description' => 'Capacidad para comprender las emociones, necesidades y perspectivas de otras personas.',
            ],
            [
                'code' => 'CRE',
                'name' => 'Creatividad',
                'description' => 'Capacidad para generar ideas originales y encontrar nuevas maneras de abordar una situación.',
            ],
            [
                'code' => 'PRO',
                'name' => 'Resolución de problemas',
                'description' => 'Capacidad para analizar dificultades, identificar alternativas y tomar decisiones efectivas.',
            ],
            [
                'code' => 'GTI',
                'name' => 'Gestión del tiempo',
                'description' => 'Capacidad para organizar actividades, establecer prioridades y utilizar el tiempo eficientemente.',
            ],
        ];

        foreach ($skills as $skill) {
            /*
             * updateOrCreate hace que el seeder sea idempotente.
             */
            Skill::query()->updateOrCreate(
                [
                    'code' => $skill['code'],
                ],
                [
                    'name' => $skill['name'],
                    'description' => $skill['description'],
                    'is_active' => true,
                ],
            );
        }
    }
}
