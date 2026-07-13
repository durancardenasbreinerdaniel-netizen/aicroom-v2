<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea los cuestionarios principales.
     */
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table): void {
            $table->id();

            $table
                ->string('name', 150)
                ->unique();

            $table
                ->string('slug', 170)
                ->unique();

            $table
                ->text('description')
                ->nullable();

            /*
             * Solo los cuestionarios activos podrán utilizarse
             * para iniciar nuevas evaluaciones.
             */
            $table
                ->boolean('is_active')
                ->default(true)
                ->index();

            $table->timestamps();

            /*
             * Permite retirar cuestionarios sin destruir
             * versiones ni información histórica.
             */
            $table->softDeletes();
        });
    }

    /**
     * Elimina la tabla.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};