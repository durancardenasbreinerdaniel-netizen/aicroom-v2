<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSkill extends CreateRecord
{
    /**
     * Recurso asociado.
     */
    protected static string $resource = SkillResource::class;

    /**
     * Redirige al listado después de crear.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Mensaje mostrado después de crear.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Habilidad creada correctamente';
    }
}
