<?php

use App\Enums\EvaluationStatus;
use App\Enums\EvaluationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea las evaluaciones y las preguntas seleccionadas para cada una.
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table): void {
            $table->id();

            /*
             * Identificador público utilizado en las rutas.
             *
             * Evita exponer el ID numérico incremental.
             */
            $table
                ->ulid('public_id')
                ->unique();

            /*
             * Participante propietario de la evaluación.
             */
            $table
                ->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            /*
             * Versión exacta del cuestionario utilizado.
             */
            $table
                ->foreignId('questionnaire_version_id')
                ->constrained()
                ->restrictOnDelete();

            $table
                ->string('evaluation_type', 20)
                ->default(EvaluationType::BASELINE->value)
                ->index();

            $table
                ->string('status', 20)
                ->default(EvaluationStatus::IN_PROGRESS->value)
                ->index();

            /*
             * Cantidad de preguntas seleccionadas al iniciar.
             */
            $table
                ->unsignedSmallInteger('total_questions');

            /*
             * Tiempo límite copiado desde la versión publicada.
             */
            $table
                ->unsignedSmallInteger('time_limit_minutes');

            $table->timestamp('started_at');

            /*
             * La fecha de expiración se calcula en el servidor.
             */
            $table
                ->timestamp('expires_at')
                ->index();

            $table
                ->timestamp('submitted_at')
                ->nullable();

            $table
                ->timestamp('cancelled_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'questionnaire_version_id',
                'status',
            ]);
        });

        Schema::create(
            'evaluation_items',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('evaluation_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                 * Conserva la referencia al ítem de la versión publicada.
                 */
                $table
                    ->foreignId('questionnaire_item_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                 * Orden específico de la pregunta dentro de esta
                 * evaluación.
                 */
                $table
                    ->unsignedSmallInteger('position');

                /*
                 * Copia inmutable utilizada durante la evaluación.
                 */
                $table
                    ->foreignId('skill_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table
                    ->string('statement', 500);

                $table
                    ->unsignedTinyInteger('weight');

                $table
                    ->boolean('is_reverse_scored');

                $table->timestamps();

                /*
                 * Una pregunta no puede repetirse en la misma evaluación.
                 */
                $table->unique([
                    'evaluation_id',
                    'questionnaire_item_id',
                ]);

                /*
                 * Cada posición debe ser única dentro de la evaluación.
                 */
                $table->unique([
                    'evaluation_id',
                    'position',
                ]);

                $table->index([
                    'evaluation_id',
                    'skill_id',
                ]);
            }
        );
    }

    /**
     * Elimina las tablas en orden inverso.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_items');
        Schema::dropIfExists('evaluations');
    }
};
