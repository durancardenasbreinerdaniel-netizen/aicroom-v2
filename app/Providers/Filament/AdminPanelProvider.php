<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    /**
     * Configura el panel administrativo de AICROOM.
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            /*
             * Define este panel como el panel principal
             * de Filament dentro de la aplicación.
             */
            ->default()

            /*
             * Identificador interno del panel.
             */
            ->id('admin')

            /*
             * El panel estará disponible bajo la ruta /admin.
             */
            ->path('admin')

            /*
             * Activa la pantalla de inicio de sesión de Filament.
             *
             * Los usuarios y permisos administrativos se configurarán
             * en la siguiente fase.
             */
            ->login()

            /*
             * Nombre que aparecerá dentro del panel.
             */
            ->brandName('AICROOM')

            /*
             * Utiliza la paleta incorporada de Filament.
             * No es necesario crear CSS personalizado.
             */
            ->colors([
                'primary' => Color::Blue,
            ])

            /*
             * Detecta automáticamente los recursos administrativos.
             */
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )

            /*
             * Detecta automáticamente las páginas personalizadas.
             */
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )

            /*
             * Registra el dashboard inicial.
             */
            ->pages([
                Dashboard::class,
            ])

            /*
             * Detecta automáticamente los widgets propios.
             */
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )

            /*
             * Muestra la información de la cuenta autenticada.
             */
            ->widgets([
                AccountWidget::class,
            ])

            /*
             * Middleware web necesario para cookies, sesiones,
             * CSRF, rutas y eventos de Filament.
             */
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            /*
             * Protege todas las páginas internas del panel.
             */
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
