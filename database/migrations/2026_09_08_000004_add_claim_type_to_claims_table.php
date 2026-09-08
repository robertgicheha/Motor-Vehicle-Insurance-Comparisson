<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Plain string, not enum — matches the existing `status` column's
            // convention on this table, and avoids the MySQL enum-widening
            // pain hit earlier on certificate_documents.document_type.
            $table->string('claim_type')->nullable()->after('incident_description');
            $table->text('claim_type_other')->nullable()->after('claim_type');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['claim_type', 'claim_type_other']);
        });
    }
};
