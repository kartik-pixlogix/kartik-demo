<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Launch brief §6 enquiry form: City, Brief Requirement and consent to be
 * contacted. Nullable so leads received before launch stay valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('city', 80)->nullable()->after('company');
            $table->text('requirement')->nullable()->after('service');
            $table->timestamp('consented_at')->nullable()->after('requirement');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['city', 'requirement', 'consented_at']);
        });
    }
};
