<?php

namespace App\Filament\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    /**
     * Configura el listado del banco de preguntas.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('skill.name')
                    ->label('Habilidad')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('statement')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(100)
                    ->wrap(),

                TextColumn::make('weight')
                    ->label('Peso')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_reverse_scored')
                    ->label('Inversa')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                SelectFilter::make('skill_id')
                    ->label('Habilidad')
                    ->relationship(
                        name: 'skill',
                        titleAttribute: 'name',
                    )
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Activas')
                    ->falseLabel('Inactivas'),

                TernaryFilter::make('is_reverse_scored')
                    ->label('Tipo de puntuación')
                    ->placeholder('Todas')
                    ->trueLabel('Inversas')
                    ->falseLabel('Directas'),

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
            ->defaultSort('created_at', 'desc')
            ->stackedOnMobile();
    }
}
