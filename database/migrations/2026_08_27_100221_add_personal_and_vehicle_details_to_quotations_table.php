<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('salutation')->nullable()->after('user_id');
            $table->string('first_name')->nullable()->after('salutation');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('phone')->nullable()->after('last_name');
            $table->string('email')->nullable()->after('phone');
            $table->string('chassis_number')->nullable()->after('car_registration');
            $table->string('engine_number')->nullable()->after('chassis_number');
            $table->string('body_type')->nullable()->after('engine_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'salutation', 'first_name', 'middle_name', 'last_name', 'phone', 'email',
                'chassis_number', 'engine_number', 'body_type',
            ]);
        });
    }
};
