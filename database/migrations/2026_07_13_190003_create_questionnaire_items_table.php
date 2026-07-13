<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea los ítems que componen cada versión.
     */
    public function up(): void
    {
        Schema::create(
            'questionnaire_items',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('questionnaire_version_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                 * Pregunta original del banco de preguntas.
                 */
                $table
                    ->foreignId('question_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                 * Posición de la pregunta dentro de la versión.
                 */
                $table
                    ->unsignedSmallInteger('position');

                /*
                 * Los siguientes campos forman la copia inmutable.
                 *
                 * Permanecen vacíos mientras la versión sea borrador
                 * y se completan al publicarla.
                 */
                $table
                    ->foreignId('skill_id_snapshot')
                    ->nullable()
                    ->constrained('skills')
                    ->restrictOnDelete();

                $table
                    ->string('statement_snapshot', 500)
                    ->nullable();

                $table
                    ->unsignedTinyInteger('weight_snapshot')
                    ->nullable();

                $table
                    ->boolean('is_reverse_scored_snapshot')
                    ->nullable();

                $table->timestamps();

                /*
                 * Una pregunta no puede repetirse dentro de la versión.
                 */
                $table->unique([
                    'questionnaire_version_id',
                    'question_id',
                ]);

                /*
                 * Dos preguntas no pueden compartir la misma posición.
                 */
                $table->unique([
                    'questionnaire_version_id',
                    'position',
                ]);
            }
        );
    }

    /**
     * Elimina la tabla.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_items');
    }
};