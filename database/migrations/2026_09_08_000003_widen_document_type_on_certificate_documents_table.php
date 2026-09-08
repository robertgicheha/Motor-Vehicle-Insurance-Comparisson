<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // document_type is a native MySQL ENUM('id_copy','kra_pin','logbook') —
        // Schema::table()->enum()->change() goes through Doctrine DBAL, which
        // doesn't understand MySQL's ENUM type, so the column is widened with
        // a raw ALTER TABLE instead (the standard approach for MySQL enums).
        DB::statement("ALTER TABLE certificate_documents MODIFY document_type ENUM('id_copy','kra_pin','logbook','other') NOT NULL");

        // The unique(sticker_id, document_type) index assumed one row per
        // type per sticker — true for the three fixed slots (a re-upload
        // replaces the existing row in code), but it structurally forbids
        // more than one 'other' row per sticker, which is the entire point
        // of the alternate-documents feature (multiple independent uploads).
        //
        // That composite index is also the only one covering sticker_id, so
        // it's silently propping up the sticker_id foreign key — MySQL
        // refuses to drop it until a plain index on sticker_id exists to
        // take over that job.
        Schema::table('certificate_documents', function ($table) {
            $table->index('sticker_id');
        });
        Schema::table('certificate_documents', function ($table) {
            $table->dropUnique(['sticker_id', 'document_type']);
        });
    }

    public function down(): void
    {
        // Reversing requires no 'other' rows to exist (an enum can't shrink
        // past values still in use, and the unique index can't be restored
        // while duplicates exist).
        DB::statement("DELETE FROM certificate_documents WHERE document_type = 'other'");
        Schema::table('certificate_documents', function ($table) {
            $table->unique(['sticker_id', 'document_type']);
        });
        Schema::table('certificate_documents', function ($table) {
            $table->dropIndex(['sticker_id']);
        });
        DB::statement("ALTER TABLE certificate_documents MODIFY document_type ENUM('id_copy','kra_pin','logbook') NOT NULL");
    }
};
