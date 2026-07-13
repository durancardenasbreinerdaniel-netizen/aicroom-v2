<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestionnaire extends CreateRecord
{
    protected static string $resource =
        QuestionnaireResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl(
            'edit',
            [
                'record' => $this->record,
            ]
        );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cuestionario creado correctamente';
    }
}