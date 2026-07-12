<main class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-6xl items-center px-6 py-12">
    <flux:card class="w-full space-y-8">
        <div class="space-y-4">
            <flux:badge
                color="blue"
                size="sm"
            >
                AICROOM V2
            </flux:badge>

            <div class="space-y-3">
                <flux:heading
                    level="1"
                    size="xl"
                >
                    Base visual configurada
                </flux:heading>

                <flux:text size="lg">
                    Plataforma para la evaluación, desarrollo y seguimiento
                    de habilidades blandas.
                </flux:text>
            </div>
        </div>

        <flux:separator />

        <div class="grid gap-4 md:grid-cols-3">
            <flux:card class="space-y-3">
                <flux:icon
                    name="bolt"
                    class="size-6 text-blue-600"
                />

                <flux:heading size="lg">
                    Livewire 4
                </flux:heading>

                <flux:text>
                    Gestionará la interacción y el estado de las páginas
                    sin construir una SPA separada.
                </flux:text>
            </flux:card>

            <flux:card class="space-y-3">
                <flux:icon
                    name="squares-2x2"
                    class="size-6 text-blue-600"
                />

                <flux:heading size="lg">
                    Flux UI 2
                </flux:heading>

                <flux:text>
                    Proporcionará componentes reutilizables, responsive
                    y compatibles con modo oscuro.
                </flux:text>
            </flux:card>

            <flux:card class="space-y-3">
                <flux:icon
                    name="wrench-screwdriver"
                    class="size-6 text-blue-600"
                />

                <flux:heading size="lg">
                    Filament 5
                </flux:heading>

                <flux:text>
                    Se utilizará exclusivamente para las herramientas
                    administrativas del sistema.
                </flux:text>
            </flux:card>
        </div>

        <flux:callout
            icon="information-circle"
            variant="secondary"
        >
            <flux:callout.heading>
                Fase 2 completada
            </flux:callout.heading>

            <flux:callout.text>
                La aplicación ya dispone de una base visual construida con
                componentes, sin depender de hojas de estilos personalizadas.
            </flux:callout.text>
        </flux:callout>

        <div>
            <flux:button
                href="/admin"
                variant="primary"
                color="blue"
                icon:trailing="arrow-right"
            >
                Abrir panel administrativo
            </flux:button>
        </div>
    </flux:card>
</main>