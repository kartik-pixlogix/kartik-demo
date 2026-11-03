<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();

            // Which <x-art.*> component illustrates the card. The artwork is hand-drawn
            // inline SVG, not uploaded imagery, so this is a key rather than a file path.
            $table->string('cover_art_key')->nullable();

            // `.art.k` — the light card frame, used by every other card in the row
            $table->boolean('art_inverted')->default(false);

            // The prototype prints "[DD MMM 2026]" rather than a real date, so the
            // display string is kept separate from the sortable timestamp.
            $table->string('date_label')->nullable();
            $table->timestamp('published_at')->nullable()->index();

            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();

            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
