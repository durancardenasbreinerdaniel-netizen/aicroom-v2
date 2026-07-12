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

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-md space-y-6">
            <div class="text-center">
                <flux:brand
                    href="{{ route('home') }}"
                    name="AICROOM"
                    class="justify-center"
                />
            </div>

            {{ $slot }}
        </div>
    </main>

    @livewireScripts
    @fluxScripts
</body>
</html>