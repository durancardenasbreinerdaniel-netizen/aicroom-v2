<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Crea los roles y permisos iniciales del sistema.
     */
    public function run(): void
    {
        /*
         * Limpia la caché interna del paquete antes de modificar
         * roles o permisos.
         */
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminPanelPermission = Permission::firstOrCreate([
            'name' => PermissionName::ACCESS_ADMIN_PANEL->value,
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => RoleName::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $participantRole = Role::firstOrCreate([
            'name' => RoleName::PARTICIPANT->value,
            'guard_name' => 'web',
        ]);

        /*
         * El administrador recibe el permiso para entrar a Filament.
         */
        $adminRole->syncPermissions([
            $adminPanelPermission,
        ]);

        /*
         * El participante no recibe permisos administrativos.
         */
        $participantRole->syncPermissions([]);

        /*
         * Limpia nuevamente la caché para que los cambios
         * estén disponibles inmediatamente.
         */
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
