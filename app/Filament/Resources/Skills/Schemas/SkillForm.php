<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SkillForm
{
    /**
     * Configura el formulario de habilidades.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la habilidad')
                    ->description(
                        'Define el código, nombre y descripción de la habilidad blanda.'
                    )
                    ->schema([
                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->minLength(3)
                            ->maxLength(10)
                            ->rules([
                                'regex:/^[A-Za-z]{3,10}$/',
                            ])
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn (?string $state): string => Str::upper(
                                    trim($state ?? '')
                                )
                            )
                            ->helperText(
                                'Entre 3 y 10 letras. Se almacenará en mayúsculas.'
                            ),

                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->minLength(3)
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn (?string $state): string => Str::squish(
                                    $state ?? ''
                                )
                            ),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(1000)
                            ->rows(5)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Habilidad activa')
                            ->helperText(
                                'Solo las habilidades activas podrán utilizarse en nuevas evaluaciones.'
                            )
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(2),
            ]);
    }
}
