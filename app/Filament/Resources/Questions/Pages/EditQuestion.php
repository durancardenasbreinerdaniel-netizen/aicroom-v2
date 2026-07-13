<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    /**
     * Recurso asociado.
     */
    protected static string $resource = QuestionResource::class;

    /**
     * Acciones disponibles.
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
     * Regresa al listado después de guardar.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Mensaje de confirmación.
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pregunta actualizada correctamente';
    }
}
