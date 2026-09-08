<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            // 'week' | 'month' | 'year' — drives the auto-calculated
            // cover_end_date rather than letting it be typed freely.
            $table->string('cover_duration_unit')->nullable()->after('cover_end_date');
            $table->unsignedInteger('cover_duration_value')->nullable()->after('cover_duration_unit');
        });
    }

    public function down(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn(['cover_duration_unit', 'cover_duration_value']);
        });
    }
};
