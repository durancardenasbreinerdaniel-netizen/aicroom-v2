<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    /**
     * Recurso asociado.
     */
    protected static string $resource = QuestionResource::class;

    /**
     * Regresa al listado después de crear.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Mensaje de confirmación.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pregunta creada correctamente';
    }
}
