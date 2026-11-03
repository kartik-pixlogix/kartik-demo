<?php

namespace App\Support;

use Filament\Support\Colors\Color;

/**
 * The V7 palette as Filament colour ramps.
 *
 * Filament needs eleven shades per colour and resolves each one to an
 * `oklch()` string. Color::hex() will generate a ramp from a single seed, but
 * it keeps only the hue — it replaces lightness and chroma with its own fixed
 * curve, so the brand hex itself never appears in the output. That is fine for
 * the shades nobody looks at directly and wrong for the ones that paint
 * buttons, surfaces and hairlines.
 *
 * So each ramp here is generated for its shape and then has the shades that
 * actually show pinned back to the exact brand value. The hard rule in
 * CLAUDE.md holds: #061522, #0C2944, #1479D4, #27B8ED, #F4F6F8 and #C7D0D8 are
 * the only colours, and they land on the tokens where they are visible.
 *
 * SEMANTIC COLOURS
 *
 * danger, warning and success are left as Filament ships them. They are not
 * brand colour — they are the difference between "saved" and "this will delete
 * three records", and a destructive confirmation rendered in Bright Blue would
 * be a worse admin. They appear only in notifications and confirmation modals,
 * never in the panel's chrome. Logged in docs/prototype-fixes.md.
 */
class BrandPalette
{
    public const PAPER = '#061522';      // Deep Navy

    public const MID = '#0C2944';        // Midnight Blue

    public const BLUE = '#1479D4';       // Bright Blue

    public const ACID = '#27B8ED';       // Cyan

    public const INK = '#F4F6F8';        // Silver White

    public const COOL = '#C7D0D8';       // Cool Silver

    /**
     * Every ramp the panel overrides.
     *
     * @return array<string, array<int, string>>
     */
    public static function colors(): array
    {
        return [
            'primary' => self::primary(),
            'gray' => self::gray(),
            'info' => self::info(),
        ];
    }

    /**
     * Bright Blue. Shade 500/600 paint buttons and links in dark mode, 600/700
     * in light, so the brand hex is pinned across that span.
     *
     * @return array<int, string>
     */
    private static function primary(): array
    {
        return self::pin(Color::hex(self::BLUE), [
            500 => self::ACID,
            600 => self::BLUE,
        ]);
    }

    /**
     * Cyan, used for the `info` slot — badges and the accents that are
     * informational rather than actionable.
     *
     * @return array<int, string>
     */
    private static function info(): array
    {
        return self::pin(Color::hex(self::ACID), [
            400 => self::ACID,
            500 => self::ACID,
        ]);
    }

    /**
     * The one that does the work.
     *
     * Filament's default gray is Zinc, which is what makes an untouched panel
     * look like every other untouched panel. Every surface, hairline and piece
     * of body text in the admin resolves through this ramp, so replacing it
     * with the site's own navy-to-silver axis rebrands the panel wholesale:
     *
     *   gray-950  page background (dark)      -> Deep Navy
     *   gray-900  cards, tables, inputs       -> Midnight Blue
     *   gray-300  muted text (dark)           -> Cool Silver
     *   gray-50   page background (light)     -> Silver White
     *
     * The intermediate shades interpolate in OKLCH along the same hue so the
     * ramp stays perceptually even. Note that 950 is *less* saturated than
     * 900: that is true of the brand colours themselves, not a mistake.
     *
     * @return array<int, string>
     */
    private static function gray(): array
    {
        return [
            50 => Color::convertToOklch(self::INK),
            100 => 'oklch(0.945 0.006 247.9)',
            200 => 'oklch(0.902 0.011 246.3)',
            300 => Color::convertToOklch(self::COOL),
            400 => 'oklch(0.742 0.022 247.2)',
            500 => 'oklch(0.627 0.030 248.0)',
            600 => 'oklch(0.515 0.038 248.6)',
            700 => 'oklch(0.412 0.049 249.2)',
            800 => 'oklch(0.330 0.057 249.5)',
            900 => Color::convertToOklch(self::MID),
            950 => Color::convertToOklch(self::PAPER),
        ];
    }

    /**
     * Replace individual shades of a generated ramp with exact brand values.
     *
     * @param  array<int, string>  $ramp
     * @param  array<int, string>  $pins  shade => brand hex
     * @return array<int, string>
     */
    private static function pin(array $ramp, array $pins): array
    {
        foreach ($pins as $shade => $hex) {
            $ramp[$shade] = Color::convertToOklch($hex);
        }

        return $ramp;
    }
}
