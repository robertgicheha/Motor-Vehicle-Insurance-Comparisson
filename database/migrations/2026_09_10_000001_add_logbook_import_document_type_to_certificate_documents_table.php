<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * document_type is a native MySQL ENUM, widened before for 'other' and
     * 'certificate_of_incorporation' (see 2026_09_08_000003 and
     * 2026_09_09_000002). Corporate clients now have a dedicated third
     * required slot — "Vehicle Logbook / Import Documents" — distinct from
     * individual's 'logbook' slot so it never inherits that slot's N/A
     * toggle (which only makes sense for an individual's own vehicle).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE certificate_documents MODIFY document_type ENUM('id_copy','kra_pin','logbook','certificate_of_incorporation','logbook_import','other') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("DELETE FROM certificate_documents WHERE document_type = 'logbook_import'");
        DB::statement("ALTER TABLE certificate_documents MODIFY document_type ENUM('id_copy','kra_pin','logbook','certificate_of_incorporation','other') NOT NULL");
    }
};
