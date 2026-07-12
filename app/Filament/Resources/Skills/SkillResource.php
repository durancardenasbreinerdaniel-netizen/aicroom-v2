<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Resources\Skills\Pages\CreateSkill;
use App\Filament\Resources\Skills\Pages\EditSkill;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Filament\Resources\Skills\Schemas\SkillForm;
use App\Filament\Resources\Skills\Tables\SkillsTable;
use App\Models\Skill;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SkillResource extends Resource
{
    /**
     * Modelo administrado por el recurso.
     *
     * @var class-string<Skill>|null
     */
    protected static ?string $model = Skill::class;

    /**
     * Icono mostrado en la navegación.
     */
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    /**
     * Campo utilizado para identificar el registro.
     */
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Etiqueta singular.
     */
    protected static ?string $modelLabel = 'habilidad';

    /**
     * Etiqueta plural.
     */
    protected static ?string $pluralModelLabel = 'habilidades';

    /**
     * Etiqueta del menú.
     */
    protected static ?string $navigationLabel = 'Habilidades';

    /**
     * Posición dentro del menú administrativo.
     */
    protected static ?int $navigationSort = 1;

    /**
     * Configura el formulario.
     */
    public static function form(Schema $schema): Schema
    {
        return SkillForm::configure($schema);
    }

    /**
     * Configura la tabla.
     */
    public static function table(Table $table): Table
    {
        return SkillsTable::configure($table);
    }

    /**
     * Incluye los registros eliminados lógicamente.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Registra las páginas del recurso.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSkills::route('/'),
            'create' => CreateSkill::route('/create'),
            'edit' => EditSkill::route('/{record}/edit'),
        ];
    }
}
