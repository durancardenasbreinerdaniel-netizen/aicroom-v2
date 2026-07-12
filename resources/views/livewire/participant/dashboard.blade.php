<main class="mx-auto max-w-6xl px-6 py-10">
    <div class="space-y-8">
        <div class="space-y-2">
            <flux:heading
                level="1"
                size="xl"
            >
                Bienvenido, {{ auth()->user()->name }}
            </flux:heading>

            <flux:text size="lg">
                Este será tu espacio para realizar evaluaciones y consultar
                tu progreso.
            </flux:text>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <flux:card class="space-y-3">
                <flux:icon
                    name="clipboard-document-check"
                    class="size-6"
                />

                <flux:heading size="lg">
                    Evaluaciones
                </flux:heading>

                <flux:text>
                    Todavía no has realizado evaluaciones.
                </flux:text>
            </flux:card>

            <flux:card class="space-y-3">
                <flux:icon
                    name="chart-bar"
                    class="size-6"
                />

                <flux:heading size="lg">
                    Resultados
                </flux:heading>

                <flux:text>
                    Tus resultados aparecerán aquí al completar una evaluación.
                </flux:text>
            </flux:card>

            <flux:card class="space-y-3">
                <flux:icon
                    name="arrow-trending-up"
                    class="size-6"
                />

                <flux:heading size="lg">
                    Evolución
                </flux:heading>

                <flux:text>
                    Podrás comparar tus evaluaciones futuras.
                </flux:text>
            </flux:card>
        </div>

        <flux:callout
            icon="information-circle"
            variant="secondary"
        >
            <flux:callout.heading>
                Cuenta de participante activa
            </flux:callout.heading>

            <flux:callout.text>
                Tu cuenta fue configurada correctamente. El módulo de
                evaluaciones será implementado en una fase posterior.
            </flux:callout.text>
        </flux:callout>
    </div>
</main>