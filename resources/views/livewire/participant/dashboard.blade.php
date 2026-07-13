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
                Este es tu espacio para realizar evaluaciones
                y consultar tu progreso.
            </flux:text>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <flux:card class="space-y-4">
                <flux:icon
                    name="clipboard-document-check"
                    class="size-6"
                />

                <flux:heading size="lg">
                    Evaluaciones
                </flux:heading>

                <flux:text>
                    Inicia una evaluación de habilidades blandas.
                </flux:text>

                <flux:button
                    href="{{ route('evaluations.index') }}"
                    variant="primary"
                    icon:trailing="arrow-right"
                >
                    Ver evaluaciones
                </flux:button>
            </flux:card>

            <flux:card class="space-y-4">
                <flux:icon
                    name="chart-bar"
                    class="size-6"
                />
                    
                <flux:heading size="lg">
                    Resultados
                </flux:heading>
            
                <flux:text>
                    Consulta los resultados de las evaluaciones
                    que hayas finalizado.
                </flux:text>
            
                <flux:button
                    href="{{ route('results.index') }}"
                    variant="primary"
                    icon:trailing="arrow-right"
                >
                    Ver resultados
                </flux:button>
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
                    Podrás comparar tus futuras evaluaciones.
                </flux:text>
            </flux:card>
        </div>
    </div>
</main>