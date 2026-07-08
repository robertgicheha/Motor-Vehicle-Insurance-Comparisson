<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->enum('company_type', ['private', 'psv', 'both'])->default('both')->after('is_active');
        });

        DB::statement("UPDATE companies SET company_type = 'both' WHERE min_sum_insured_psv IS NOT NULL AND min_sum_insured_private IS NOT NULL");
        DB::statement("UPDATE companies SET company_type = 'psv' WHERE min_sum_insured_psv IS NOT NULL AND min_sum_insured_private IS NULL");
        DB::statement("UPDATE companies SET company_type = 'private' WHERE min_sum_insured_psv IS NULL AND min_sum_insured_private IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('company_type');
        });
    }
};
