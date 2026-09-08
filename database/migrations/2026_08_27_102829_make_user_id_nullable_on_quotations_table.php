<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The public quote flow creates quotations before the visitor logs in,
        // so user_id is legitimately NULL for guest quotes. Forcing NOT NULL
        // back on with such rows present fails with SQLSTATE[22004] and aborts
        // the whole rollback part-way through — which is worse than not
        // reversing this one column. Only restore the constraint when the data
        // actually allows it; otherwise leave it nullable and say why.
        $guestQuotes = DB::table('quotations')->whereNull('user_id')->count();

        if ($guestQuotes > 0) {
            echo "  Skipped: quotations.user_id left nullable — {$guestQuotes} guest "
               . "quotation(s) have no user_id, so NOT NULL cannot be restored.\n";

            return;
        }

        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
