<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_settings', function (Blueprint $table) {
            $table->boolean('letterhead_logo_header')->default(true)->after('letterhead_logo');
            $table->boolean('letterhead_logo_footer')->default(true)->after('letterhead_logo_header');
        });
    }

    public function down(): void
    {
        Schema::table('email_settings', function (Blueprint $table) {
            $table->dropColumn(['letterhead_logo_header', 'letterhead_logo_footer']);
        });
    }
};
