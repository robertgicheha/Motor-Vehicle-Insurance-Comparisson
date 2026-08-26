<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sticker_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', ['id_copy', 'kra_pin', 'logbook']);
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->enum('scan_status', ['pending', 'clean', 'infected', 'error'])->default('pending');
            $table->text('scan_result')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['sticker_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_documents');
    }
};
