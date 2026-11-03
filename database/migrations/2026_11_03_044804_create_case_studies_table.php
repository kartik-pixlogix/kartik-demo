<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            // "BFSI / GRC / VAPT / MSS" — the kicker under the card title
            $table->string('tags')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();

            $table->string('cover_art_key')->nullable();
            $table->boolean('art_inverted')->default(false);

            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
