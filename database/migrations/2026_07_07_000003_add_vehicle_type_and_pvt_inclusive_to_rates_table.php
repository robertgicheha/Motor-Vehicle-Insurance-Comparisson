<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->string('vehicle_type')->default('psv')->after('notes');
            $table->boolean('is_pvt_inclusive')->default(false)->after('vehicle_type');
        });
    }

    public function down(): void
    {
        Schema::table('rates', function (Blueprint $table) {
            $table->dropColumn(['vehicle_type', 'is_pvt_inclusive']);
        });
    }
};
