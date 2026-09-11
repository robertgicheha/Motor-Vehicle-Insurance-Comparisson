<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * claim_number is our own internal tracking reference, generated the
     * moment a claim is logged — never the underwriter's official claim
     * number, which only exists once they've reviewed and acknowledged the
     * claim. Mirrors stickers.underwriter_sticker_no.
     */
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->string('underwriter_claim_number')->nullable()->after('claim_number');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('underwriter_claim_number');
        });
    }
};
