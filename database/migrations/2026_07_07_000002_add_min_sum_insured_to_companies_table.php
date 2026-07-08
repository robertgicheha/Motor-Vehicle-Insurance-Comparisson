<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('min_sum_insured_psv', 15, 2)->nullable()->after('is_active');
            $table->decimal('min_sum_insured_private', 15, 2)->nullable()->after('min_sum_insured_psv');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['min_sum_insured_psv', 'min_sum_insured_private']);
        });
    }
};
