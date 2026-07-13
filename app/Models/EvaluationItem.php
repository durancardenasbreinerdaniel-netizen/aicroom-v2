<?php

namespace App\Models;

use Database\Factories\EvaluationItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EvaluationItem extends Model
{
    /** @use HasFactory<EvaluationItemFactory> */
    use HasFactory;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'evaluation_id',
        'questionnaire_item_id',
        'position',
        'skill_id',
        'statement',
        'weight',
        'is_reverse_scored',
    ];

    /**
     * Conversiones de tipos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'weight' => 'integer',
            'is_reverse_scored' => 'boolean',
        ];
    }

    /**
     * Evaluación propietaria.
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Ítem original de la versión publicada.
     */
    public function questionnaireItem(): BelongsTo
    {
        return $this->belongsTo(
            QuestionnaireItem::class,
        );
    }

    /**
     * Habilidad evaluada.
     */
    public function skill(): BelongsTo
    {
        return $this
            ->belongsTo(Skill::class)
            ->withTrashed();
    }

    /**
     * Respuesta asociada con esta pregunta.
     */
    public function answer(): HasOne
    {
        return $this->hasOne(Answer::class);
    }
}
