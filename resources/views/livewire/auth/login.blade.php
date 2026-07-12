<flux:card class="space-y-6">
    <div class="space-y-2">
        <flux:heading
            level="1"
            size="xl"
        >
            Iniciar sesión
        </flux:heading>

        <flux:text>
            Ingresa con tu cuenta para continuar en AICROOM.
        </flux:text>
    </div>

    <form
        wire:submit="login"
        class="space-y-5"
    >
        <flux:input
            wire:model.blur="form.email"
            label="Correo electrónico"
            type="email"
            autocomplete="email"
            required
            autofocus
        />

        <flux:input
            wire:model="form.password"
            label="Contraseña"
            type="password"
            autocomplete="current-password"
            viewable
            required
        />

        <flux:checkbox
            wire:model="form.remember"
            label="Mantener la sesión iniciada"
        />

        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
        >
            <span wire:loading.remove wire:target="login">
                Ingresar
            </span>

            <span wire:loading wire:target="login">
                Verificando...
            </span>
        </flux:button>
    </form>

    <flux:separator />

    <flux:text class="text-center">
        ¿Todavía no tienes una cuenta?

        <flux:link href="{{ route('register') }}">
            Crear cuenta
        </flux:link>
    </flux:text>
</flux:card>