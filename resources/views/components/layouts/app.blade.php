<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="min-h-full"
>
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="description"
        content="AICROOM: evaluación, desarrollo y seguimiento de habilidades blandas."
    >

    <title>
        {{ $title ?? config('app.name', 'AICROOM') }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    {{-- Estilos necesarios para los componentes Livewire. --}}
    @livewireStyles

    {{--
        Flux administra la apariencia clara, oscura o basada
        en la configuración del sistema operativo.
    --}}
    @fluxAppearance
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-950 dark:bg-zinc-950 dark:text-white">
    <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
            <flux:brand
                :href="route('home')"
                name="AICROOM"
            />

            <flux:button
                href="/admin"
                variant="ghost"
                icon="lock-closed"
            >
                Administración
            </flux:button>
        </div>
    </header>

    {{--
        El contenido de cada página Livewire será insertado
        dentro de este espacio.
    --}}
    {{ $slot }}

    {{-- Scripts principales de Livewire y Flux. --}}
    @livewireScripts
    @fluxScripts
</body>
</html>