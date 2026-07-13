<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea las respuestas de las evaluaciones.
     */
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table): void {
            $table->id();

            /*
             * Evaluación a la que pertenece la respuesta.
             */
            $table
                ->foreignId('evaluation_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
             * Pregunta específica de la evaluación.
             *
             * Cada ítem solo puede recibir una respuesta.
             */
            $table
                ->foreignId('evaluation_item_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            /*
             * Valor seleccionado en la escala Likert:
             *
             * 1 = Nunca
             * 2 = Rara vez
             * 3 = Algunas veces
             * 4 = Frecuentemente
             * 5 = Siempre
             */
            $table
                ->unsignedTinyInteger('answer_value');

            /*
             * Momento de la última actualización de la respuesta.
             */
            $table->timestamp('answered_at');

            $table->timestamps();

            $table->index([
                'evaluation_id',
                'answered_at',
            ]);
        });
    }

    /**
     * Elimina la tabla de respuestas.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
