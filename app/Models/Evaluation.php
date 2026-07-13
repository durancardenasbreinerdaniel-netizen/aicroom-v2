<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use App\Enums\EvaluationType;
use Database\Factories\EvaluationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Evaluation extends Model
{
    /** @use HasFactory<EvaluationFactory> */
    use HasFactory;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'public_id',
        'user_id',
        'questionnaire_version_id',
        'evaluation_type',
        'status',
        'total_questions',
        'time_limit_minutes',
        'started_at',
        'expires_at',
        'submitted_at',
        'cancelled_at',
    ];

    /**
     * Configura los valores automáticos del modelo.
     */
    protected static function booted(): void
    {
        static::creating(function (Evaluation $evaluation): void {
            /*
             * Genera un ULID antes de guardar la evaluación.
             */
            if (blank($evaluation->public_id)) {
                $evaluation->public_id = (string) Str::ulid();
            }
        });
    }

    /**
     * Conversiones de tipos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluation_type' => EvaluationType::class,
            'status' => EvaluationStatus::class,
            'total_questions' => 'integer',
            'time_limit_minutes' => 'integer',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Utiliza el identificador público para el route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    /**
     * Participante propietario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Versión del cuestionario utilizada.
     */
    public function questionnaireVersion(): BelongsTo
    {
        return $this->belongsTo(
            QuestionnaireVersion::class
        );
    }

    /**
     * Preguntas seleccionadas para la evaluación.
     */
    public function items(): HasMany
    {
        return $this
            ->hasMany(EvaluationItem::class)
            ->orderBy('position');
    }

    /**
     * Limita una consulta a evaluaciones en progreso.
     */
    #[Scope]
    protected function inProgress(Builder $query): void
    {
        $query->where(
            'status',
            EvaluationStatus::IN_PROGRESS->value
        );
    }

    /**
     * Limita una consulta al propietario indicado.
     */
    #[Scope]
    protected function ownedBy(
        Builder $query,
        User|int $user
    ): void {
        $userId = $user instanceof User
            ? $user->id
            : $user;

        $query->where('user_id', $userId);
    }

    /**
     * Indica si el tiempo disponible terminó.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Indica si la evaluación acepta acciones del participante.
     */
    public function isInProgress(): bool
    {
        return $this->status === EvaluationStatus::IN_PROGRESS;
    }

    /**
     * Marca la evaluación como expirada.
     */
    public function markAsExpired(): void
    {
        if (! $this->isInProgress()) {
            return;
        }

        $this->forceFill([
            'status' => EvaluationStatus::EXPIRED,
        ])->save();
    }

    /**
     * Devuelve los segundos restantes.
     */
    public function remainingSeconds(): int
    {
        if ($this->hasExpired()) {
            return 0;
        }

        return (int) now()->diffInSeconds(
            $this->expires_at
        );
    }

    /**
     * Respuestas almacenadas en la evaluación.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Cantidad de preguntas respondidas.
     */
    public function answeredQuestionsCount(): int
    {
        return $this->answers()->count();
    }

    /**
     * Porcentaje de avance de la evaluación.
     */
    public function progressPercentage(): int
    {
        if ($this->total_questions === 0) {
            return 0;
        }

        return (int) round(
            ($this->answeredQuestionsCount() / $this->total_questions) * 100,
        );
    }

    /**
     * Indica si todas las preguntas fueron respondidas.
     */
    public function isFullyAnswered(): bool
    {
        return $this->answeredQuestionsCount()
            === $this->total_questions;
    }

    /**
     * Resultado calculado de la evaluación.
     */
    public function result(): HasOne
    {
        return $this->hasOne(
            EvaluationResult::class,
        );
    }
}
