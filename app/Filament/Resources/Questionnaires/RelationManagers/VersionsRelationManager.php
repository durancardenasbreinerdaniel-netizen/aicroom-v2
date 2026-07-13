<?php

namespace App\Filament\Resources\Questionnaires\RelationManagers;

use App\Actions\Questionnaires\PublishQuestionnaireVersion;
use App\Enums\QuestionnaireVersionStatus;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class VersionsRelationManager extends RelationManager
{
    /**
     * Relación definida en Questionnaire.
     */
    protected static string $relationship = 'versions';

    /**
     * Título visible.
     */
    protected static ?string $title = 'Versiones';

    /**
     * Configura el formulario de las versiones.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuración')
                    ->schema([
                        TextInput::make(
                            'questions_per_evaluation'
                        )
                            ->label('Preguntas por evaluación')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(30)
                            ->required(),

                        TextInput::make('time_limit_minutes')
                            ->label('Tiempo límite')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(240)
                            ->suffix('minutos')
                            ->default(20)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Banco de la versión')
                    ->description(
                        'Selecciona y ordena las preguntas disponibles para esta versión.'
                    )
                    ->schema([
                        Repeater::make('items')
                            ->label('Preguntas')
                            ->relationship()
                            ->orderColumn('position')
                            ->schema([
                                Select::make('question_id')
                                    ->label('Pregunta')
                                    ->relationship(
                                        name: 'question',
                                        titleAttribute: 'statement',
                                        modifyQueryUsing:
                                            fn (Builder $query): Builder => $query
                                                ->whereNull('deleted_at')
                                                ->where(
                                                    'is_active',
                                                    true
                                                )
                                                ->whereHas(
                                                    'skill',
                                                    fn (Builder $skillQuery): Builder => $skillQuery
                                                        ->whereNull('deleted_at')
                                                        ->where(
                                                            'is_active',
                                                            true
                                                        )
                                                )
                                                ->orderBy('statement'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->minItems(1)
                            ->addActionLabel('Agregar pregunta')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Configura el listado de versiones.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('version_number')
            ->columns([
                TextColumn::make('version_number')
                    ->label('Versión')
                    ->formatStateUsing(
                        fn (int $state): string => "v{$state}"
                    )
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(
                        fn (
                            QuestionnaireVersionStatus $state
                        ): string => $state->label()
                    )
                    ->color(
                        fn (
                            QuestionnaireVersionStatus $state
                        ): string => $state->color()
                    ),

                TextColumn::make('items_count')
                    ->label('Banco')
                    ->counts('items')
                    ->suffix(' preguntas'),

                TextColumn::make('questions_per_evaluation')
                    ->label('Por evaluación'),

                TextColumn::make('time_limit_minutes')
                    ->label('Tiempo')
                    ->suffix(' min'),

                TextColumn::make('published_at')
                    ->label('Publicada')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin publicar'),

                TextColumn::make('publisher.full_name')
                    ->label('Publicada por')
                    ->placeholder('Sin publicar'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva versión')
                    ->visible(
                        fn (): bool => ! $this
                            ->getOwnerRecord()
                            ->versions()
                            ->where(
                                'status',
                                QuestionnaireVersionStatus::DRAFT->value
                            )
                            ->exists()
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(
                        fn (
                            QuestionnaireVersion $record
                        ): bool => $record->isDraft()
                    ),

                Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Publicar versión')
                    ->modalDescription(
                        'La versión quedará bloqueada y no podrá modificarse después de publicarla.'
                    )
                    ->visible(
                        fn (
                            QuestionnaireVersion $record
                        ): bool => $record->isDraft()
                    )
                    ->action(
                        function (
                            QuestionnaireVersion $record
                        ): void {
                            Gate::authorize(
                                'publish',
                                $record
                            );

                            app(
                                PublishQuestionnaireVersion::class
                            )->execute(
                                $record,
                                auth()->user()
                            );

                            Notification::make()
                                ->title(
                                    'Versión publicada correctamente'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                DeleteAction::make()
                    ->visible(
                        fn (
                            QuestionnaireVersion $record
                        ): bool => $record->isDraft()
                    ),
            ])
            ->defaultSort('version_number', 'desc');
    }
}