<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea los resultados globales y los resultados por habilidad.
     */
    public function up(): void
    {
        Schema::create(
            'evaluation_results',
            function (Blueprint $table): void {
                $table->id();

                /*
                 * Cada evaluación puede tener un único resultado.
                 */
                $table
                    ->foreignId('evaluation_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                 * Puntajes acumulados antes de la normalización.
                 */
                $table->unsignedInteger('raw_score');
                $table->unsignedInteger('minimum_score');
                $table->unsignedInteger('maximum_score');

                /*
                 * Puntaje transformado al intervalo de 0 a 100.
                 */
                $table->decimal(
                    'normalized_score',
                    5,
                    2,
                );

                $table
                    ->string('level', 20)
                    ->index();

                /*
                 * Permite identificar qué versión del algoritmo
                 * produjo el resultado.
                 */
                $table
                    ->string('algorithm_version', 20);

                $table->timestamp('calculated_at');

                $table->timestamps();
            }
        );

        Schema::create(
            'evaluation_skill_results',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('evaluation_result_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                 * Conserva la relación con la habilidad original.
                 */
                $table
                    ->foreignId('skill_id')
                    ->constrained()
                    ->restrictOnDelete();

                /*
                 * Copia el código y el nombre para preservar
                 * la información histórica.
                 */
                $table
                    ->string('skill_code_snapshot', 10);

                $table
                    ->string('skill_name_snapshot', 100);

                $table->unsignedInteger('raw_score');
                $table->unsignedInteger('minimum_score');
                $table->unsignedInteger('maximum_score');

                $table->decimal(
                    'normalized_score',
                    5,
                    2,
                );

                $table
                    ->string('level', 20)
                    ->index();

                $table->timestamps();

                /*
                 * Una habilidad solo puede aparecer una vez
                 * dentro del resultado de la evaluación.
                 */
                $table->unique([
                    'evaluation_result_id',
                    'skill_id',
                ]);
            }
        );
    }

    /**
     * Elimina las tablas en el orden inverso.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'evaluation_skill_results'
        );

        Schema::dropIfExists(
            'evaluation_results'
        );
    }
};
