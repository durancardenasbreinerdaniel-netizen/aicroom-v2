<?php

namespace App\Models;

use App\Enums\EvaluationLevel;
use Database\Factories\EvaluationResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationResult extends Model
{
    /** @use HasFactory<EvaluationResultFactory> */
    use HasFactory;

    /**
     * Versión actual del algoritmo de cálculo.
     */
    public const ALGORITHM_VERSION = '1.0.0';

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'evaluation_id',
        'raw_score',
        'minimum_score',
        'maximum_score',
        'normalized_score',
        'level',
        'algorithm_version',
        'calculated_at',
    ];

    /**
     * Conversiones de tipos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'raw_score' => 'integer',
            'minimum_score' => 'integer',
            'maximum_score' => 'integer',
            'normalized_score' => 'decimal:2',
            'level' => EvaluationLevel::class,
            'calculated_at' => 'datetime',
        ];
    }

    /**
     * Evaluación que produjo el resultado.
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Resultados individuales por habilidad.
     */
    public function skillResults(): HasMany
    {
        return $this
            ->hasMany(EvaluationSkillResult::class)
            ->orderByDesc('normalized_score');
    }
}
