<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    /**
     * Recurso asociado.
     */
    protected static string $resource = QuestionResource::class;

    /**
     * Acciones del encabezado.
     *
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva pregunta'),
        ];
    }
}
