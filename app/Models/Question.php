<?php

namespace App\Models;

use App\Enums\LikertValue;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'skill_id',
        'statement',
        'weight',
        'is_reverse_scored',
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
            'weight' => 'integer',
            'is_reverse_scored' => 'boolean',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Habilidad que evalúa esta pregunta.
     */
    public function skill(): BelongsTo
    {
        /*
         * withTrashed permite seguir consultando la habilidad
         * asociada aunque haya sido eliminada lógicamente.
         */
        return $this
            ->belongsTo(Skill::class)
            ->withTrashed();
    }

    /**
     * Normaliza el enunciado antes de almacenarlo.
     *
     * @return Attribute<string, string>
     */
    protected function statement(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::squish($value),
        );
    }

    /**
     * Limita la consulta a preguntas activas.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Devuelve el valor que debe utilizar el motor de puntuación.
     */
    public function scoredValue(LikertValue|int $answer): int
    {
        $likertValue = $answer instanceof LikertValue
            ? $answer
            : LikertValue::from($answer);

        if ($this->is_reverse_scored) {
            return $likertValue->reversed()->value;
        }

        return $likertValue->value;
    }

    /**
     * Calcula el puntaje ponderado de una respuesta.
     */
    public function weightedScore(LikertValue|int $answer): int
    {
        return $this->scoredValue($answer) * $this->weight;
    }

    /**
     * Devuelve el puntaje mínimo posible para esta pregunta.
     */
    public function minimumWeightedScore(): int
    {
        return LikertValue::minimum() * $this->weight;
    }

    /**
     * Devuelve el puntaje máximo posible para esta pregunta.
     */
    public function maximumWeightedScore(): int
    {
        return LikertValue::maximum() * $this->weight;
    }

    /**
     * Ítems de cuestionario que utilizan esta pregunta.
     */
    public function questionnaireItems(): HasMany
    {
        return $this->hasMany(QuestionnaireItem::class);
    }
}
