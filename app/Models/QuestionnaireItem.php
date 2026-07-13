<?php

namespace App\Models;

use Database\Factories\QuestionnaireItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireItem extends Model
{
    /** @use HasFactory<QuestionnaireItemFactory> */
    use HasFactory;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'questionnaire_version_id',
        'question_id',
        'position',
        'skill_id_snapshot',
        'statement_snapshot',
        'weight_snapshot',
        'is_reverse_scored_snapshot',
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
            'weight_snapshot' => 'integer',
            'is_reverse_scored_snapshot' => 'boolean',
        ];
    }

    /**
     * Versión propietaria.
     */
    public function questionnaireVersion(): BelongsTo
    {
        return $this->belongsTo(
            QuestionnaireVersion::class
        );
    }

    /**
     * Pregunta original.
     */
    public function question(): BelongsTo
    {
        return $this
            ->belongsTo(Question::class)
            ->withTrashed();
    }

    /**
     * Habilidad almacenada al publicar.
     */
    public function skillSnapshot(): BelongsTo
    {
        return $this
            ->belongsTo(
                Skill::class,
                'skill_id_snapshot'
            )
            ->withTrashed();
    }

    /**
     * Copia los valores actuales de la pregunta.
     */
    public function createSnapshotFromQuestion(
        Question $question
    ): void {
        $this->forceFill([
            'skill_id_snapshot' => $question->skill_id,
            'statement_snapshot' => $question->statement,
            'weight_snapshot' => $question->weight,
            'is_reverse_scored_snapshot' => $question->is_reverse_scored,
        ])->save();
    }

    /**
     * Indica si el ítem ya posee una copia inmutable.
     */
    public function hasSnapshot(): bool
    {
        return $this->skill_id_snapshot !== null
            && $this->statement_snapshot !== null
            && $this->weight_snapshot !== null
            && $this->is_reverse_scored_snapshot !== null;
    }
}