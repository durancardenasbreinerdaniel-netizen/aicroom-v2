<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestionnaires extends ListRecords
{
    protected static string $resource =
        QuestionnaireResource::class;

    /**
     * Acciones del encabezado.
     *
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo cuestionario'),
        ];
    }
}
