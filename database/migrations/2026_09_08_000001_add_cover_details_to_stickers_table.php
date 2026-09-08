<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->date('cover_start_date')->nullable()->after('cover_type');
            $table->date('cover_end_date')->nullable()->after('cover_start_date');
            // 'individual' | 'corporate' — whether the vehicle is owned by a
            // person or a company, captured before KYC upload since it changes
            // what supporting documents the underwriter actually needs.
            $table->string('client_type')->default('individual')->after('cover_end_date');
            // Set when the logbook genuinely isn't available yet (e.g. new
            // vehicle, financed/pending transfer) so the required-document
            // check in StickerController::issue() accepts alternate ("other")
            // documents in its place instead of blocking issuance.
            $table->boolean('logbook_na')->default(false)->after('client_type');
        });
    }

    public function down(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn(['cover_start_date', 'cover_end_date', 'client_type', 'logbook_na']);
        });
    }
};
