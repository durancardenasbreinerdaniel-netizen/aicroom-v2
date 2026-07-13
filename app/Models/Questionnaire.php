<?php

namespace App\Models;

use App\Enums\QuestionnaireVersionStatus;
use Database\Factories\QuestionnaireFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Questionnaire extends Model
{
    /** @use HasFactory<QuestionnaireFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Conversiones de tipos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Versiones del cuestionario.
     */
    public function versions(): HasMany
    {
        return $this
            ->hasMany(QuestionnaireVersion::class)
            ->orderByDesc('version_number');
    }

    /**
     * Versión publicada actualmente.
     */
    public function publishedVersion(): HasOne
    {
        return $this
            ->hasOne(QuestionnaireVersion::class)
            ->where(
                'status',
                QuestionnaireVersionStatus::PUBLISHED->value
            );
    }

    /**
     * Versión que todavía se encuentra en borrador.
     */
    public function draftVersion(): HasOne
    {
        return $this
            ->hasOne(QuestionnaireVersion::class)
            ->where(
                'status',
                QuestionnaireVersionStatus::DRAFT->value
            );
    }

    /**
     * Normaliza el nombre.
     *
     * @return Attribute<string, string>
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::squish($value),
        );
    }

    /**
     * Normaliza el slug.
     *
     * @return Attribute<string, string>
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::slug($value),
        );
    }

    /**
     * Normaliza la descripción.
     *
     * @return Attribute<string|null, string|null>
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): ?string {
                if ($value === null || trim($value) === '') {
                    return null;
                }

                return trim($value);
            },
        );
    }

    /**
     * Limita la consulta a cuestionarios activos.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
