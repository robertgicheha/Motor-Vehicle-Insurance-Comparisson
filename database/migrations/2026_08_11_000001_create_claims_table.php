<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('on_behalf_of_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('registration_number');
            $table->json('vehicle_details')->nullable();
            $table->date('incident_date')->nullable();
            $table->text('incident_description')->nullable();
            $table->string('police_abstract_reference')->nullable();
            $table->decimal('settlement_amount', 15, 2)->nullable();
            $table->text('settlement_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->string('status')->default('reporting');
            $table->boolean('vehicle_assessment_done')->default(false);
            $table->timestamps();
        });

        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->string('document_type');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('scan_status')->default('pending');
            $table->string('ai_validation_status')->default('pending');
            $table->text('ai_validation_notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_documents');
        Schema::dropIfExists('claims');
    }
};
