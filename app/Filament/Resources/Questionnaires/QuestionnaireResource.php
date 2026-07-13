<?php

namespace App\Filament\Resources\Questionnaires;

use App\Filament\Resources\Questionnaires\Pages\CreateQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\EditQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\ListQuestionnaires;
use App\Filament\Resources\Questionnaires\RelationManagers\VersionsRelationManager;
use App\Filament\Resources\Questionnaires\Schemas\QuestionnaireForm;
use App\Filament\Resources\Questionnaires\Tables\QuestionnairesTable;
use App\Models\Questionnaire;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionnaireResource extends Resource
{
    /**
     * Modelo administrado.
     *
     * @var class-string<Questionnaire>|null
     */
    protected static ?string $model = Questionnaire::class;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'cuestionario';

    protected static ?string $pluralModelLabel = 'cuestionarios';

    protected static ?string $navigationLabel = 'Cuestionarios';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return QuestionnaireForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionnairesTable::configure($table);
    }

    /**
     * Registra la administración de versiones.
     *
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [
            VersionsRelationManager::class,
        ];
    }

    /**
     * Incluye cuestionarios eliminados lógicamente.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Registra las páginas.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListQuestionnaires::route('/'),
            'create' => CreateQuestionnaire::route('/create'),
            'edit' => EditQuestionnaire::route('/{record}/edit'),
        ];
    }
}