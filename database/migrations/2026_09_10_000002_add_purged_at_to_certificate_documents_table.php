<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracks when a KYC document's underlying file was deleted from disk —
     * done immediately once the email carrying it to the underwriter has
     * sent (see StickerController::issue()), never retained afterwards.
     * The row itself (document_type, filename, scan_status) is kept for audit
     * purposes — only the file content and its 'path' are removed.
     */
    public function up(): void
    {
        Schema::table('certificate_documents', function (Blueprint $table) {
            $table->timestamp('purged_at')->nullable()->after('scan_result');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_documents', function (Blueprint $table) {
            $table->dropColumn('purged_at');
        });
    }
};
