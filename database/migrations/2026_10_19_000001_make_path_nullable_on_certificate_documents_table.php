<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The zero-storage KYC lifecycle never writes a KYC file to disk, so
     * certificate_documents rows are audit-only and always carry a NULL path
     * (see StickerController::issue()). The original create migration declared
     * path as NOT NULL, which — under MySQL STRICT_TRANS_TABLES — made every
     * sticker request that included documents fail with
     * "Column 'path' cannot be null". Allow NULL to match the design.
     */
    public function up(): void
    {
        Schema::table('certificate_documents', function (Blueprint $table) {
            $table->string('path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificate_documents', function (Blueprint $table) {
            $table->string('path')->nullable(false)->change();
        });
    }
};
