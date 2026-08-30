<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('underwriter_name')->nullable()->after('underwriter_email');
            $table->string('underwriter_phone')->nullable()->after('underwriter_name');
            $table->string('claims_name')->nullable()->after('claims_email');
            $table->string('claims_phone')->nullable()->after('claims_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['underwriter_name', 'underwriter_phone', 'claims_name', 'claims_phone']);
        });
    }
};
