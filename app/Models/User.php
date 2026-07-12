<?php

namespace App\Models;

use App\Enums\PermissionName;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    /**
     * Atributos que pueden asignarse masivamente.
     *
     * El rol no se incluye porque se administra mediante Spatie Permission.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'password',
        'status',
        'last_login_at',
    ];

    /**
     * Atributos ocultos al serializar el modelo.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversiones de tipos realizadas por Eloquent.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',

            /*
             * Laravel aplicará hash automáticamente si se asigna
             * una contraseña que todavía no está cifrada.
             */
            'password' => 'hashed',

            /*
             * Convierte el texto almacenado en la base de datos
             * al enum UserStatus.
             */
            'status' => UserStatus::class,
        ];
    }

    /**
     * Devuelve el nombre completo del usuario.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }

    /**
     * Indica si la cuenta puede iniciar sesión.
     */
    public function canAuthenticate(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    /**
     * Controla el acceso al panel administrativo de Filament.
     *
     * No se utiliza el correo electrónico para decidir el acceso.
     * El usuario necesita estar activo y poseer el permiso correspondiente.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->canAuthenticate()
            && $this->can(PermissionName::ACCESS_ADMIN_PANEL->value);
    }
}
