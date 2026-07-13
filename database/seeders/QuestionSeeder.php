<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use RuntimeException;

class QuestionSeeder extends Seeder
{
    /**
     * Registra preguntas iniciales para desarrollo.
     *
     * Estas preguntas deberán ser revisadas y validadas por una persona
     * especializada antes de utilizar AICROOM como instrumento psicométrico
     * en producción.
     */
    public function run(): void
    {
        $questionsBySkill = [
            'LEA' => [
                [
                    'statement' => 'Tomo la iniciativa cuando un grupo necesita avanzar hacia un objetivo.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Ayudo a otras personas a organizarse y comprender lo que deben hacer.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Evito asumir responsabilidades cuando el equipo necesita orientación.',
                    'is_reverse_scored' => true,
                ],
            ],

            'COM' => [
                [
                    'statement' => 'Expreso mis ideas de forma clara y comprensible para otras personas.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Escucho con atención antes de responder durante una conversación.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Me cuesta prestar atención cuando otra persona está explicando su punto de vista.',
                    'is_reverse_scored' => true,
                ],
            ],

            'TEA' => [
                [
                    'statement' => 'Comparto información útil para facilitar el trabajo de mi equipo.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Tengo en cuenta las opiniones del grupo antes de tomar decisiones conjuntas.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Prefiero trabajar sin colaborar incluso cuando una tarea requiere esfuerzo grupal.',
                    'is_reverse_scored' => true,
                ],
            ],

            'ADA' => [
                [
                    'statement' => 'Me ajusto con facilidad cuando cambian las condiciones de una actividad.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Busco nuevas alternativas cuando una estrategia deja de funcionar.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Me bloqueo cuando debo modificar una forma de trabajo que ya conocía.',
                    'is_reverse_scored' => true,
                ],
            ],

            'RES' => [
                [
                    'statement' => 'Cumplo los compromisos que asumo dentro de los plazos establecidos.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Reconozco mis errores y tomo acciones para corregirlos.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Dejo tareas importantes sin terminar aunque me haya comprometido a realizarlas.',
                    'is_reverse_scored' => true,
                ],
            ],

            'EST' => [
                [
                    'statement' => 'Mantengo la calma cuando debo responder ante situaciones de presión.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Organizo mis acciones para evitar que la presión afecte mis decisiones.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Pierdo el control con facilidad cuando varias dificultades ocurren al mismo tiempo.',
                    'is_reverse_scored' => true,
                ],
            ],

            'EMP' => [
                [
                    'statement' => 'Procuro comprender cómo se siente otra persona antes de juzgar su comportamiento.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Adapto mi manera de comunicarme cuando noto que alguien necesita apoyo.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Ignoro las emociones de otras personas cuando no afectan directamente mis tareas.',
                    'is_reverse_scored' => true,
                ],
            ],

            'CRE' => [
                [
                    'statement' => 'Propongo ideas diferentes cuando una situación necesita una solución nueva.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Exploro varias posibilidades antes de descartar una idea poco convencional.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Descarto rápidamente cualquier solución que no se parezca a las que ya conozco.',
                    'is_reverse_scored' => true,
                ],
            ],

            'PRO' => [
                [
                    'statement' => 'Analizo las causas de un problema antes de elegir una solución.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Comparo diferentes alternativas antes de tomar una decisión importante.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Actúo sin revisar la información disponible cuando aparece un problema.',
                    'is_reverse_scored' => true,
                ],
            ],

            'GTI' => [
                [
                    'statement' => 'Establezco prioridades para completar primero las actividades más importantes.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Planifico mi tiempo teniendo en cuenta los plazos de cada tarea.',
                    'is_reverse_scored' => false,
                ],
                [
                    'statement' => 'Suelo postergar actividades importantes hasta que queda muy poco tiempo.',
                    'is_reverse_scored' => true,
                ],
            ],
        ];

        foreach ($questionsBySkill as $skillCode => $questions) {
            $skill = Skill::query()
                ->where('code', $skillCode)
                ->first();

            if ($skill === null) {
                throw new RuntimeException(
                    "No se encontró la habilidad con código {$skillCode}."
                );
            }

            foreach ($questions as $questionData) {
                /*
                 * Se incluyen registros eliminados para que ejecutar
                 * nuevamente el seeder restaure una pregunta existente.
                 */
                Question::withTrashed()->updateOrCreate(
                    [
                        'statement' => $questionData['statement'],
                    ],
                    [
                        'skill_id' => $skill->id,
                        'weight' => 1,
                        'is_reverse_scored' => $questionData['is_reverse_scored'],
                        'is_active' => true,
                        'deleted_at' => null,
                    ],
                );
            }
        }
    }
}
