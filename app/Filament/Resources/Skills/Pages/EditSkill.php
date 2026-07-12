<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSkill extends EditRecord
{
    /**
     * Recurso asociado.
     */
    protected static string $resource = SkillResource::class;

    /**
     * Acciones disponibles en el encabezado.
     *
     * No se agrega ForceDeleteAction porque las habilidades
     * no deben eliminarse físicamente.
     *
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),

            RestoreAction::make(),
        ];
    }

    /**
     * Redirige al listado después de guardar.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Mensaje mostrado después de editar.
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Habilidad actualizada correctamente';
    }
}
