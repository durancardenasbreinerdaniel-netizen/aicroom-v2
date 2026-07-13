<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Cantidad mínima de preguntas activas requerida para utilizar
     * una habilidad dentro de un cuestionario.
     */
    public const MINIMUM_ACTIVE_QUESTIONS = 3;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
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
     * Preguntas asociadas con la habilidad.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Preguntas activas asociadas con la habilidad.
     */
    public function activeQuestions(): HasMany
    {
        return $this
            ->hasMany(Question::class)
            ->where('is_active', true);
    }

    /**
     * Indica si la habilidad tiene suficientes preguntas activas.
     */
    public function hasMinimumActiveQuestions(): bool
    {
        return $this->activeQuestions()->count()
            >= self::MINIMUM_ACTIVE_QUESTIONS;
    }

    /**
     * Normaliza el código.
     *
     * @return Attribute<string, string>
     */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::upper(
                Str::squish($value)
            ),
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
     * Limita la consulta a habilidades activas.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Resultados históricos asociados con la habilidad.
     */
    public function evaluationSkillResults(): HasMany
    {
        return $this->hasMany(
            EvaluationSkillResult::class,
        );
    }
}
