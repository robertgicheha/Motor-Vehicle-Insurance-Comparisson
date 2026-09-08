<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The ClaimDocument model (committed separately, ahead of this feature)
     * already referenced ai_validation_status/ai_validation_notes columns —
     * used by App\Services\AIDocumentValidationService, which exists but
     * isn't wired into any controller yet. Adding them here keeps that
     * service's field references valid instead of quietly breaking them
     * when claim_documents was recreated.
     */
    public function up(): void
    {
        Schema::table('claim_documents', function (Blueprint $table) {
            $table->string('ai_validation_status')->nullable()->after('scan_result');
            $table->text('ai_validation_notes')->nullable()->after('ai_validation_status');
        });
    }

    public function down(): void
    {
        Schema::table('claim_documents', function (Blueprint $table) {
            $table->dropColumn(['ai_validation_status', 'ai_validation_notes']);
        });
    }
};
