<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->string('email_subject')->nullable()->after('admin_notes');
            $table->text('email_body')->nullable()->after('email_subject');
            $table->string('email_from_address')->nullable()->after('email_body');
            $table->string('email_from_name')->nullable()->after('email_from_address');
            $table->string('reply_to_address')->nullable()->after('email_from_name');
            $table->string('last_email_status')->nullable()->after('reply_to_address')->comment('sent, delivered, failed, bounced');
            $table->text('last_email_error')->nullable()->after('last_email_status');
            $table->timestamp('email_sent_at')->nullable()->after('last_email_error');
            $table->timestamp('response_received_at')->nullable()->after('email_sent_at');
            $table->string('response_status')->nullable()->after('response_received_at')->comment('pending, received, processing, completed');
            $table->text('response_notes')->nullable()->after('response_status');
            $table->string('response_document_path')->nullable()->after('response_notes')->comment('Path to sticker/policy doc returned by underwriter');
        });
    }

    public function down(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn([
                'email_subject', 'email_body', 'email_from_address', 'email_from_name',
                'reply_to_address', 'last_email_status', 'last_email_error', 'email_sent_at',
                'response_received_at', 'response_status', 'response_notes', 'response_document_path',
            ]);
        });
    }
};
