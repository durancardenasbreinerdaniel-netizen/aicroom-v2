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

    @livewireStyles
    @fluxAppearance
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-950 dark:bg-zinc-950 dark:text-white">
    <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mx-auto flex min-h-16 max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-3">
            <flux:brand
                href="{{ route('home') }}"
                name="AICROOM"
            />

            <nav class="flex flex-wrap items-center gap-2">
                @guest
                    <flux:button
                        href="{{ route('login') }}"
                        variant="ghost"
                    >
                        Iniciar sesión
                    </flux:button>

                    <flux:button
                        href="{{ route('register') }}"
                        variant="primary"
                    >
                        Crear cuenta
                    </flux:button>
                @endguest

                @auth
                    <flux:button
                        href="{{ route('dashboard') }}"
                        variant="ghost"
                        icon="home"
                    >
                        Mi panel
                    </flux:button>

                    @can(\App\Enums\PermissionName::ACCESS_ADMIN_PANEL->value)
                        <flux:button
                            href="{{ url('/admin') }}"
                            variant="ghost"
                            icon="lock-closed"
                        >
                            Administración
                        </flux:button>
                    @endcan

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf

                        <flux:button
                            type="submit"
                            variant="ghost"
                            icon="arrow-right-start-on-rectangle"
                        >
                            Cerrar sesión
                        </flux:button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    {{ $slot }}

    @livewireScripts
    @fluxScripts
</body>
</html>