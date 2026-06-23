<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stickers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sticker_number')->unique();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->string('car_make');
            $table->string('car_model');
            $table->integer('car_year');
            $table->decimal('car_valuation', 15, 2);
            $table->decimal('premium', 15, 2);
            $table->decimal('premium_rate', 8, 4)->nullable();
            $table->string('cover_type')->default('Comprehensive');
            $table->string('status')->default('active');
            $table->string('created_via')->default('web')->comment('web, api, admin');
            $table->json('breakdown')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stickers');
    }
};
