<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea el banco de preguntas.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();

            /*
             * Cada pregunta pertenece a una única habilidad.
             *
             * Se restringe la eliminación física de la habilidad para
             * proteger la integridad de las preguntas almacenadas.
             */
            $table
                ->foreignId('skill_id')
                ->constrained()
                ->restrictOnDelete();

            /*
             * Enunciado que verá el participante.
             *
             * El índice unique evita registrar exactamente la misma
             * pregunta más de una vez.
             */
            $table
                ->string('statement', 500)
                ->unique();

            /*
             * Multiplicador utilizado por el motor de puntuación.
             *
             * Los valores permitidos estarán entre 1 y 5.
             */
            $table
                ->unsignedTinyInteger('weight')
                ->default(1);

            /*
             * Una pregunta inversa transforma las respuestas:
             *
             * 1 => 5
             * 2 => 4
             * 3 => 3
             * 4 => 2
             * 5 => 1
             */
            $table
                ->boolean('is_reverse_scored')
                ->default(false)
                ->index();

            /*
             * Las preguntas inactivas no podrán aparecer en nuevas
             * evaluaciones.
             */
            $table
                ->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();

            /*
             * Las preguntas históricas nunca se eliminan físicamente.
             */
            $table->softDeletes();

            /*
             * Mejora las consultas por habilidad y estado.
             */
            $table->index([
                'skill_id',
                'is_active',
            ]);
        });
    }

    /**
     * Elimina la tabla de preguntas.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
