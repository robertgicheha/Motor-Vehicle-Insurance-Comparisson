<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * document_type is a native MySQL ENUM (widened once already for
     * 'other' — see 2026_09_08_000003). Corporate clients need a fixed slot
     * for the Certificate of Incorporation alongside the existing kra_pin
     * slot; their third slot ("Others", CR12 etc.) reuses the existing
     * 'other' type, no new value needed for that.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE certificate_documents MODIFY document_type ENUM('id_copy','kra_pin','logbook','certificate_of_incorporation','other') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("DELETE FROM certificate_documents WHERE document_type = 'certificate_of_incorporation'");
        DB::statement("ALTER TABLE certificate_documents MODIFY document_type ENUM('id_copy','kra_pin','logbook','other') NOT NULL");
    }
};
