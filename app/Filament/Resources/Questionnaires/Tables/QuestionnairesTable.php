<?php

namespace App\Filament\Resources\Questionnaires\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class QuestionnairesTable
{
    /**
     * Configura el listado.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Cuestionario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Identificador')
                    ->badge()
                    ->searchable(),

                TextColumn::make('versions_count')
                    ->label('Versiones')
                    ->counts('versions')
                    ->badge(),

                TextColumn::make(
                    'publishedVersion.version_number'
                )
                    ->label('Versión publicada')
                    ->formatStateUsing(
                        fn ($state): string => $state !== null
                            ? "v{$state}"
                            : 'Sin publicar'
                    )
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),

                TrashedFilter::make()
                    ->label('Registros eliminados'),
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name')
            ->stackedOnMobile();
    }
}
