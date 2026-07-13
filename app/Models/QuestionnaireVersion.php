<?php

namespace App\Models;

use App\Enums\QuestionnaireVersionStatus;
use Database\Factories\QuestionnaireVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireVersion extends Model
{
    /** @use HasFactory<QuestionnaireVersionFactory> */
    use HasFactory;

    /**
     * Atributos permitidos para asignación masiva.
     *
     * @var list<string>
     */
    protected $fillable = [
        'questionnaire_id',
        'version_number',
        'questions_per_evaluation',
        'time_limit_minutes',
        'status',
        'published_at',
        'created_by',
        'published_by',
    ];

    /**
     * Conversiones de tipos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
            'questions_per_evaluation' => 'integer',
            'time_limit_minutes' => 'integer',
            'status' => QuestionnaireVersionStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * Configura valores automáticos al crear una versión.
     */
    protected static function booted(): void
    {
        static::creating(
            function (QuestionnaireVersion $version): void {
                /*
                 * Calcula automáticamente el siguiente número.
                 */
                if ($version->version_number === null) {
                    $lastVersion = self::query()
                        ->where(
                            'questionnaire_id',
                            $version->questionnaire_id
                        )
                        ->max('version_number');

                    $version->version_number = ((int) $lastVersion) + 1;
                }

                /*
                 * Registra al usuario autenticado cuando corresponda.
                 */
                if (
                    $version->created_by === null
                    && auth()->check()
                ) {
                    $version->created_by = auth()->id();
                }
            }
        );
    }

    /**
     * Cuestionario propietario.
     */
    public function questionnaire(): BelongsTo
    {
        return $this
            ->belongsTo(Questionnaire::class)
            ->withTrashed();
    }

    /**
     * Preguntas que componen la versión.
     */
    public function items(): HasMany
    {
        return $this
            ->hasMany(QuestionnaireItem::class)
            ->orderBy('position');
    }

    /**
     * Usuario que creó la versión.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Usuario que publicó la versión.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'published_by'
        );
    }

    /**
     * Indica si la versión puede editarse.
     */
    public function isDraft(): bool
    {
        return $this->status
            === QuestionnaireVersionStatus::DRAFT;
    }

    /**
     * Indica si la versión está publicada.
     */
    public function isPublished(): bool
    {
        return $this->status
            === QuestionnaireVersionStatus::PUBLISHED;
    }

    /**
     * Indica si la versión fue retirada.
     */
    public function isRetired(): bool
    {
        return $this->status
            === QuestionnaireVersionStatus::RETIRED;
    }

    /**
     * Evaluaciones creadas desde esta versión.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
