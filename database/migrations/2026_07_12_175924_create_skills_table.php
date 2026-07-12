<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de habilidades blandas.
     */
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table): void {
            $table->id();

            /*
             * Código corto y estable utilizado para identificar
             * la habilidad internamente.
             *
             * Ejemplos: COM, LEA, EMP.
             */
            $table
                ->string('code', 10)
                ->unique();

            /*
             * Nombre legible de la habilidad.
             */
            $table
                ->string('name', 100)
                ->unique();

            /*
             * Explicación general de la habilidad.
             */
            $table
                ->text('description')
                ->nullable();

            /*
             * Una habilidad inactiva no podrá utilizarse en
             * nuevas evaluaciones.
             */
            $table
                ->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();

            /*
             * Permite eliminar lógicamente la habilidad
             * sin destruir su información histórica.
             */
            $table->softDeletes();
        });
    }

    /**
     * Elimina la tabla de habilidades.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
