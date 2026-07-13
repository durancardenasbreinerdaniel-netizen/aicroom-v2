<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class QuestionForm
{
    /**
     * Configura el formulario del banco de preguntas.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenido de la pregunta')
                    ->description(
                        'Define la habilidad evaluada y el enunciado que verá el participante.'
                    )
                    ->schema([
                        Select::make('skill_id')
                            ->label('Habilidad')
                            ->relationship(
                                name: 'skill',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Textarea::make('statement')
                            ->label('Enunciado')
                            ->required()
                            ->minLength(10)
                            ->maxLength(500)
                            ->rows(4)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn (?string $state): string => Str::squish(
                                    $state ?? ''
                                )
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Configuración de puntuación')
                    ->description(
                        'Controla el peso, la dirección de puntuación y el estado de la pregunta.'
                    )
                    ->schema([
                        TextInput::make('weight')
                            ->label('Peso')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(1)
                            ->required()
                            ->helperText(
                                'Valor entero entre 1 y 5.'
                            ),

                        Toggle::make('is_reverse_scored')
                            ->label('Puntuación inversa')
                            ->helperText(
                                'Actívalo cuando una respuesta alta represente un desempeño menor.'
                            )
                            ->default(false)
                            ->inline(false),

                        Toggle::make('is_active')
                            ->label('Pregunta activa')
                            ->helperText(
                                'Solo las preguntas activas podrán utilizarse en nuevas evaluaciones.'
                            )
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(3),
            ]);
    }
}
