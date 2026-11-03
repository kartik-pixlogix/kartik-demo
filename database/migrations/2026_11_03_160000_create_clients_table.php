<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The client-logo marquee in the trust strip.
 *
 * The prototype draws sixteen `Client logo` placeholders (eight, duplicated
 * and the copies hidden from assistive tech so the loop reads once). Those are
 * placeholders in the CLAUDE.md sense: real names cannot be invented. This
 * table lets the client add their own, and the strip falls back to the
 * prototype's placeholders whenever the table is empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('url')->nullable();
            $table->string('industry')->nullable();

            /*
             * Marks a row that was seeded for review rather than supplied by
             * the client. The admin flags these and the footer of the Clients
             * list says how many are still outstanding, so a sample cannot
             * quietly end up on the live site.
             */
            $table->boolean('is_sample')->default(false);

            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['is_visible', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
