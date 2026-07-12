<?php

use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la información básica requerida por AICROOM.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            /*
             * Se almacena por separado para evitar guardar el nombre
             * completo en una sola columna difícil de consultar.
             */
            $table
                ->string('last_name')
                ->after('name');

            /*
             * El teléfono es opcional inicialmente.
             */
            $table
                ->string('phone', 30)
                ->nullable()
                ->after('email');

            /*
             * Permite bloquear o desactivar usuarios sin eliminarlos.
             */
            $table
                ->string('status', 20)
                ->default(UserStatus::ACTIVE->value)
                ->index()
                ->after('password');

            /*
             * Guarda el último inicio de sesión exitoso.
             */
            $table
                ->timestamp('last_login_at')
                ->nullable()
                ->after('status');
        });
    }

    /**
     * Revierte los campos agregados.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['status']);

            $table->dropColumn([
                'last_name',
                'phone',
                'status',
                'last_login_at',
            ]);
        });
    }
};
