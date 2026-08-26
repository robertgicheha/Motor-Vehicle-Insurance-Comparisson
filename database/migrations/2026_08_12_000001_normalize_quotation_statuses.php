<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Collapse the old multi-status workflow (processing, pending, draft,
     * expired, active, closed, insuring_sticker) into two statuses only:
     *  - generated  → the quotation has been created
     *  - completed  → an insurance certificate / sticker has been issued
     */
    public function up(): void
    {
        DB::table('quotations')->whereIn('status', [
            'processing', 'pending', 'draft', 'expired',
        ])->update(['status' => 'generated']);

        DB::table('quotations')->whereIn('status', [
            'completed', 'closed', 'insuring_sticker', 'active',
        ])->update(['status' => 'completed']);

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('status')->default('generated')->change();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('status')->default('processing')->change();
        });
    }
};
