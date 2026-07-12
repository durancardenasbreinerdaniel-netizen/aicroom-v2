<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
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
         * Limpia la caché antes de crear o actualizar permisos.
         */
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
         * Crea todos los permisos definidos en el enum.
         *
         * firstOrCreate permite ejecutar el seeder varias veces
         * sin duplicar registros.
         *
         * @var Collection<int, Permission> $permissions
         */
        $permissions = collect(PermissionName::cases())
            ->map(
                fn (PermissionName $permission): Permission => Permission::firstOrCreate([
                    'name' => $permission->value,
                    'guard_name' => 'web',
                ])
            );

        $adminRole = Role::firstOrCreate([
            'name' => RoleName::ADMIN->value,
            'guard_name' => 'web',
        ]);

        $participantRole = Role::firstOrCreate([
            'name' => RoleName::PARTICIPANT->value,
            'guard_name' => 'web',
        ]);

        /*
         * El administrador recibe todos los permisos disponibles.
         */
        $adminRole->syncPermissions($permissions);

        /*
         * El participante no recibe permisos administrativos.
         */
        $participantRole->syncPermissions([]);

        /*
         * Actualiza la caché después de guardar los cambios.
         */
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
