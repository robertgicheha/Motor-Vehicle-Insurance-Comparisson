<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sticker_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sticker_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment('outbound_to_underwriter, inbound_from_underwriter, system_notification');
            $table->string('recipient')->nullable();
            $table->string('sender')->nullable();
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('status')->comment('queued, sent, delivered, failed, bounced, opened, clicked');
            $table->text('error_message')->nullable();
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();

            $table->index(['sticker_id', 'type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sticker_email_logs');
    }
};
