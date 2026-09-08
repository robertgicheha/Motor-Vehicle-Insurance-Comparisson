<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * claim_documents was originally created then dropped in an earlier
     * migration (2026_08_13_000001_update_claims_table). Recreated here for
     * the police abstract + incident photo attachments on the claim form.
     *
     * Unlike a sticker (which gets a "pending" draft row the moment the
     * issuer picks a quotation), a claim doesn't exist as a row until the
     * whole form is submitted — so there's nothing to attach an upload to
     * yet. upload_token stages documents against a client-generated token
     * before the claim exists; claim_id is filled in right after the claim
     * is created, in the same request.
     */
    public function up(): void
    {
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->string('upload_token')->index();
            $table->foreignId('claim_id')->nullable()->constrained()->cascadeOnDelete();
            // 'police_abstract' (single slot, optional) | 'incident_photo'
            // (any number of independent rows) — plain string, see the note
            // on claims.claim_type in the sibling migration.
            $table->string('document_type');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->string('scan_status')->default('clean');
            $table->text('scan_result')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['claim_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_documents');
    }
};
