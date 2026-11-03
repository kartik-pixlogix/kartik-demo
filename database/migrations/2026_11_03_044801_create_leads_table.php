<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('company');
            $table->string('service');

            // where it came from
            $table->string('source_page')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();

            // The raw IP is never stored: this is a keyed hash, enough to spot a
            // flood from one address without holding personal data we do not need.
            // UB Shield advises clients on DPDPA data minimisation; the site follows it.
            $table->string('ip_hash', 64)->nullable()->index();
            $table->text('user_agent')->nullable();

            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
