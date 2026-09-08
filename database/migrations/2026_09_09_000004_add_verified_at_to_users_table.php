<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Distinct from email_verified_at (used only by the Google OAuth login
     * path) — this tracks admin approval of a self-registered account.
     * Self-registration now creates the account deactivated (is_active =
     * false) with verified_at left null; the "Pending Users" admin screen
     * activates it and stamps verified_at. Every existing/admin-created user
     * is backfilled to verified so this new gate cannot lock anyone out who
     * could already log in before this migration.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('is_active');
        });

        DB::table('users')->whereNull('verified_at')->update(['verified_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verified_at');
        });
    }
};
