<main class="mx-auto max-w-3xl px-6 py-10">
    <flux:card class="space-y-7">
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <flux:badge
                    color="{{ $evaluation->status->color() }}"
                >
                    {{ $evaluation->status->label() }}
                </flux:badge>

                <flux:badge color="zinc">
                    {{ $evaluation->evaluation_type->label() }}
                </flux:badge>
            </div>

            <flux:heading
                level="1"
                size="xl"
            >
                {{ $evaluation->questionnaireVersion->questionnaire->name }}
            </flux:heading>

            <flux:text size="lg">
                La evaluación fue preparada correctamente.
            </flux:text>
        </div>

        <flux:separator />

        <div class="grid gap-4 sm:grid-cols-3">
            <flux:card class="space-y-2">
                <flux:icon
                    name="question-mark-circle"
                    class="size-6"
                />

                <flux:heading size="lg">
                    {{ $evaluation->total_questions }}
                </flux:heading>

                <flux:text>
                    Preguntas
                </flux:text>
            </flux:card>

            <flux:card class="space-y-2">
                <flux:icon
                    name="clock"
                    class="size-6"
                />

                <flux:heading size="lg">
                    {{ $evaluation->time_limit_minutes }}
                </flux:heading>

                <flux:text>
                    Minutos
                </flux:text>
            </flux:card>

            <flux:card class="space-y-2">
                <flux:icon
                    name="academic-cap"
                    class="size-6"
                />

                <flux:heading size="lg">
                    {{ $evaluation->items()->distinct('skill_id')->count('skill_id') }}
                </flux:heading>

                <flux:text>
                    Habilidades
                </flux:text>
            </flux:card>
        </div>

        @if ($evaluation->isInProgress())
            <flux:callout
                icon="information-circle"
                variant="secondary"
            >
                <flux:callout.heading>
                    Evaluación iniciada
                </flux:callout.heading>

                <flux:callout.text>
                    El módulo para responder las preguntas será agregado
                    en la siguiente fase.
                </flux:callout.text>
            </flux:callout>
        @else
            <flux:callout
                icon="exclamation-triangle"
                variant="danger"
            >
                <flux:callout.heading>
                    La evaluación ya no está disponible
                </flux:callout.heading>

                <flux:callout.text>
                    Su estado actual es:
                    {{ $evaluation->status->label() }}.
                </flux:callout.text>
            </flux:callout>
        @endif

        <flux:button
            href="{{ route('evaluations.index') }}"
            variant="ghost"
            icon="arrow-left"
        >
            Volver a evaluaciones
        </flux:button>
    </flux:card>
</main>