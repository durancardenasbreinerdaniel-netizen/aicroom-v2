<?php

use App\Enums\QuestionnaireVersionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea las versiones de los cuestionarios.
     */
    public function up(): void
    {
        Schema::create(
            'questionnaire_versions',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('questionnaire_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                 * Número secuencial dentro de cada cuestionario.
                 */
                $table
                    ->unsignedInteger('version_number');

                /*
                 * Cantidad de preguntas que tendrá una evaluación.
                 *
                 * El banco de la versión puede contener más preguntas
                 * que la cantidad seleccionada para cada evaluación.
                 */
                $table
                    ->unsignedSmallInteger('questions_per_evaluation')
                    ->default(30);

                /*
                 * Tiempo máximo expresado en minutos.
                 */
                $table
                    ->unsignedSmallInteger('time_limit_minutes')
                    ->default(20);

                $table
                    ->string('status', 20)
                    ->default(
                        QuestionnaireVersionStatus::DRAFT->value
                    )
                    ->index();

                $table
                    ->timestamp('published_at')
                    ->nullable();

                /*
                 * Usuario que creó la versión.
                 */
                $table
                    ->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                /*
                 * Usuario que publicó la versión.
                 */
                $table
                    ->foreignId('published_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamps();

                /*
                 * Un cuestionario no puede repetir un número de versión.
                 */
                $table->unique([
                    'questionnaire_id',
                    'version_number',
                ]);
            }
        );
    }

    /**
     * Elimina la tabla.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_versions');
    }
};
