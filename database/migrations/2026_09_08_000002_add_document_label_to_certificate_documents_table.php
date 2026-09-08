<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_documents', function (Blueprint $table) {
            // Free-text label for document_type = 'other' rows, e.g. "Driver's
            // Licence" — the fixed types (id_copy/kra_pin/logbook) don't need
            // this, their label is implicit from the type itself.
            $table->string('document_label')->nullable()->after('document_type');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_documents', function (Blueprint $table) {
            $table->dropColumn('document_label');
        });
    }
};
