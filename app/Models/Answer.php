<?php

namespace App\Models;

use App\Enums\LikertValue;
use Database\Factories\AnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    /** @use HasFactory<AnswerFactory> */
    use HasFactory;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'evaluation_id',
        'evaluation_item_id',
        'answer_value',
        'answered_at',
    ];

    /**
     * Conversiones automáticas.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answer_value' => LikertValue::class,
            'answered_at' => 'datetime',
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
     * Pregunta respondida.
     */
    public function evaluationItem(): BelongsTo
    {
        return $this->belongsTo(EvaluationItem::class);
    }
}
