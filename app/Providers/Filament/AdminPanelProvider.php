<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LeadsByService;
use App\Filament\Widgets\LeadsOverview;
use App\Filament\Widgets\RecentLeads;
use App\Support\BrandPalette;
use App\Support\LocalAvatarProvider;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
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

            /*
             * BRANDING
             *
             * Filament ships amber on white with Inter, which is a fine
             * default and looks nothing like this company. Everything below
             * puts the panel on the same palette, type and shapes as the
             * site, so an administrator moving between the two does not feel
             * they have left the product.
             *
             * The colour ramps are generated from the six brand hexes — see
             * App\Support\BrandPalette for which shade each one is pinned to
             * and why the semantic colours are left alone.
             */
            ->colors(BrandPalette::colors())

            /*
             * Dark first. The site is Deep Navy and so is the admin; light
             * mode still works and is still reachable from the user menu.
             */
            ->defaultThemeMode(ThemeMode::Dark)

            /*
             * Instrument Sans, self-hosted from public/fonts — the site's own
             * body face. The @font-face lives in the brand stylesheet below,
             * so LocalFontProvider is passed with no URL: it sets
             * --font-family without emitting a second stylesheet link.
             * Without an explicit provider Filament would reach for
             * fonts.bunny.net, which the admin CSP blocks anyway.
             */
            ->font('Instrument Sans', provider: LocalFontProvider::class)
            ->monoFont('Michroma', provider: LocalFontProvider::class)

            /*
             * Everything edited in here is published on the public site, and
             * there was no way to get to it from the panel without typing the
             * URL.
             */
            ->userMenuItems([
                Action::make('view-site')
                    ->label('View the site')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (): string => route('home'))
                    ->openUrlInNewTab(),
            ])

            ->brandName(config('app.name'))
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->brandLogoHeight('1.75rem')
            ->favicon(asset('images/brand/icon-192.png'))

            /*
             * The brand layer. Registered on STYLES_AFTER rather than through
             * ->assets(), because @filamentStyles renders *before* the theme
             * stylesheet — a Css asset would be overridden by the very rules
             * it is meant to override. This hook is the first point after
             * both the theme and the generated token block.
             */
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<link rel="stylesheet" href="'
                    .e(Vite::asset('resources/css/filament-brand.css')).'">',
            )

            /*
             * The sidebar is deliberately NOT collapsible. Filament renders
             * it collapsed on first paint and expands it from localStorage,
             * which flashes on every page load, and the collapse toggle sits
             * hard against the logo. Five navigation items do not need the
             * room back.
             */
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

            /*
             * The default dashboard is Filament's AccountWidget alone — a card
             * that says the signed-in user's name and offers a sign-out button
             * that is already in the user menu. Replaced with the three things
             * someone opening this panel actually wants to know: what has come
             * in, what it was about, and what still needs their attention.
             */
            ->widgets([
                LeadsOverview::class,
                LeadsByService::class,
                RecentLeads::class,
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
