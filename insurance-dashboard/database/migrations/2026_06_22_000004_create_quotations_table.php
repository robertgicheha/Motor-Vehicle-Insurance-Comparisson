<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number')->unique();
            $table->string('car_make');
            $table->string('car_model');
            $table->integer('car_year');
            $table->string('car_registration')->nullable();
            $table->decimal('car_valuation', 15, 2);
            $table->decimal('premium', 15, 2);
            $table->decimal('pvt_premium', 15, 2)->default(0);
            $table->decimal('total_premium', 15, 2);
            $table->decimal('excess_own_damage', 15, 2)->nullable();
            $table->decimal('excess_theft', 15, 2)->nullable();
            $table->string('status')->default('pending');
            $table->json('breakdown')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->string('coverage')->nullable();
            $table->decimal('limit_amount', 15, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
