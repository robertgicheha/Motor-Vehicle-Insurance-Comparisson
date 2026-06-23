<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->unsignedInteger('download_count')->default(0)->after('generated_at');
            $table->timestamp('last_downloaded_at')->nullable()->after('download_count');
        });
    }

    public function down(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn(['download_count', 'last_downloaded_at']);
        });
    }
};
