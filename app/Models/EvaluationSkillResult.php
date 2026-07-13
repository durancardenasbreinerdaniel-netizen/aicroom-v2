<?php

namespace App\Models;

use App\Enums\EvaluationLevel;
use Database\Factories\EvaluationSkillResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationSkillResult extends Model
{
    /** @use HasFactory<EvaluationSkillResultFactory> */
    use HasFactory;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'evaluation_result_id',
        'skill_id',
        'skill_code_snapshot',
        'skill_name_snapshot',
        'raw_score',
        'minimum_score',
        'maximum_score',
        'normalized_score',
        'level',
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
        ];
    }

    /**
     * Resultado global propietario.
     */
    public function evaluationResult(): BelongsTo
    {
        return $this->belongsTo(
            EvaluationResult::class,
        );
    }

    /**
     * Habilidad original.
     */
    public function skill(): BelongsTo
    {
        return $this
            ->belongsTo(Skill::class)
            ->withTrashed();
    }
}
