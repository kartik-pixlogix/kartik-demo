<?php

namespace App\Providers\Filament;

use App\Support\LocalAvatarProvider;
use Filament\Auth\MultiFactor\App\AppAuthentication;
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
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(isSimple: false)

            /*
             * Two-factor authentication (BUILD.md Phase 4).
             *
             * Authenticator-app TOTP with recovery codes, and isRequired() so
             * it cannot be skipped — a new administrator is taken straight to
             * setup and cannot reach the panel until it is done. This admin
             * holds every lead the site has ever captured, so optional 2FA
             * would be the wrong default for a security company.
             */
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->regenerableRecoveryCodes(),
            ], isRequired: true)

            /*
             * Login throttling. Filament's own limiter, keyed per user and IP.
             * The password policy lives in App\Rules\StrongPassword and is
             * applied through Password::defaults() in AppServiceProvider.
             */
            ->loginRouteSlug('login')

            /*
             * Filament's default avatar provider is ui-avatars.com, which
             * sends the signed-in user's name to a third party on every admin
             * page load. Rendered locally instead — see the class docblock.
             */
            ->defaultAvatarProvider(LocalAvatarProvider::class)

            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
