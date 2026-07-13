<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Schemas\QuestionForm;
use App\Filament\Resources\Questions\Tables\QuestionsTable;
use App\Models\Question;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    /**
     * Modelo administrado.
     *
     * @var class-string<Question>|null
     */
    protected static ?string $model = Question::class;

    /**
     * Icono del menú.
     */
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    /**
     * Campo utilizado para identificar cada registro.
     */
    protected static ?string $recordTitleAttribute = 'statement';

    /**
     * Etiquetas del recurso.
     */
    protected static ?string $modelLabel = 'pregunta';

    protected static ?string $pluralModelLabel = 'preguntas';

    protected static ?string $navigationLabel = 'Preguntas';

    /**
     * Orden dentro del menú.
     */
    protected static ?int $navigationSort = 2;

    /**
     * Configura el formulario.
     */
    public static function form(Schema $schema): Schema
    {
        return QuestionForm::configure($schema);
    }

    /**
     * Configura la tabla.
     */
    public static function table(Table $table): Table
    {
        return QuestionsTable::configure($table);
    }

    /**
     * Incluye preguntas eliminadas lógicamente.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Páginas disponibles.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }
}
