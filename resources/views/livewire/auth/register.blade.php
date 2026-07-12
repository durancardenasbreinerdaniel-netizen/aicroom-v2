<flux:card class="space-y-6">
    <div class="space-y-2">
        <flux:heading
            level="1"
            size="xl"
        >
            Crear cuenta
        </flux:heading>

        <flux:text>
            Regístrate para comenzar tu proceso de evaluación y desarrollo.
        </flux:text>
    </div>

    <form
        wire:submit="register"
        class="space-y-5"
    >
        <div class="grid gap-5 sm:grid-cols-2">
            <flux:input
                wire:model.blur="form.name"
                label="Nombre"
                type="text"
                autocomplete="given-name"
                required
            />

            <flux:input
                wire:model.blur="form.lastName"
                label="Apellido"
                type="text"
                autocomplete="family-name"
                required
            />
        </div>

        <flux:input
            wire:model.blur="form.email"
            label="Correo electrónico"
            type="email"
            autocomplete="email"
            required
        />

        <flux:input
            wire:model.blur="form.phone"
            label="Teléfono"
            type="tel"
            autocomplete="tel"
            description="Este campo es opcional."
        />

        <flux:input
            wire:model="form.password"
            label="Contraseña"
            type="password"
            autocomplete="new-password"
            viewable
            required
        />

        <flux:input
            wire:model="form.passwordConfirmation"
            label="Confirmar contraseña"
            type="password"
            autocomplete="new-password"
            viewable
            required
        />

        <flux:button
            type="submit"
            variant="primary"
            class="w-full"
        >
            <span wire:loading.remove wire:target="register">
                Crear cuenta
            </span>

            <span wire:loading wire:target="register">
                Creando cuenta...
            </span>
        </flux:button>
    </form>

    <flux:separator />

    <flux:text class="text-center">
        ¿Ya tienes una cuenta?

        <flux:link href="{{ route('login') }}">
            Inicia sesión
        </flux:link>
    </flux:text>
</flux:card>