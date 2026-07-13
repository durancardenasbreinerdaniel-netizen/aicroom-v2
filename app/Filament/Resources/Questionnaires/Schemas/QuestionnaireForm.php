<?php

namespace App\Filament\Resources\Questionnaires\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class QuestionnaireForm
{
    /**
     * Configura el formulario.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del cuestionario')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->minLength(3)
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn (?string $state): string => Str::squish(
                                    $state ?? ''
                                )
                            ),

                        TextInput::make('slug')
                            ->label('Identificador')
                            ->required()
                            ->maxLength(170)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn (?string $state): string => Str::slug(
                                    $state ?? ''
                                )
                            )
                            ->helperText(
                                'Ejemplo: evaluacion-general'
                            ),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Cuestionario activo')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }
}