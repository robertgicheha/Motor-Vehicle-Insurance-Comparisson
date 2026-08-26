<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->foreignId('issued_by')->nullable()->after('quotation_id')->constrained('users')->nullOnDelete();
            $table->json('kyc_documents')->nullable()->after('breakdown')->comment('Paths to uploaded KYC documents');
            $table->string('underwriter_email')->nullable()->after('customer_email');
            $table->text('admin_notes')->nullable()->after('underwriter_email');
        });
    }

    public function down(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropForeign(['issued_by']);
            $table->dropColumn(['issued_by', 'kyc_documents', 'underwriter_email', 'admin_notes']);
        });
    }
};
