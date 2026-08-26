<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_settings', function (Blueprint $table) {
            $table->string('letterhead_company')->nullable()->after('type');
            $table->string('letterhead_color')->nullable()->after('letterhead_company');
            $table->string('letterhead_logo')->nullable()->after('letterhead_color');
            $table->text('letterhead_address')->nullable()->after('letterhead_logo');
            $table->string('letterhead_website')->nullable()->after('letterhead_address');
        });
    }

    public function down(): void
    {
        Schema::table('email_settings', function (Blueprint $table) {
            $table->dropColumn([
                'letterhead_company',
                'letterhead_color',
                'letterhead_logo',
                'letterhead_address',
                'letterhead_website',
            ]);
        });
    }
};
