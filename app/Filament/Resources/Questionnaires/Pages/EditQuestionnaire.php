<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditQuestionnaire extends EditRecord
{
    protected static string $resource =
        QuestionnaireResource::class;

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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Cuestionario actualizado correctamente';
    }
}
