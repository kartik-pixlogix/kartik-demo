<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An optional uploaded cover for field notes and case studies.
 *
 * Until now the only artwork available to a content record was one of the
 * hand-drawn SVGs in resources/views/components/art (see App\Support\CoverArt),
 * which is what keeps the cards inside the design system and out of stock
 * photography. That stays the default.
 *
 * This column is the override: when it is set the image is used, when it is
 * null the SVG renders exactly as before. Nothing about the existing records
 * changes, and clearing the field puts the drawing back.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['insights', 'case_studies'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('cover_image')->nullable()->after('cover_art_key');
            });
        }
    }

    public function down(): void
    {
        foreach (['insights', 'case_studies'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('cover_image');
            });
        }
    }
};
