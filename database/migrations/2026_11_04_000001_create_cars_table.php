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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->string('slug', 160)->unique();
            $table->unsignedSmallInteger('year');
            $table->string('license_plate', 20);
            $table->enum('transmission', ['Manual', 'Automatic', 'CVT', 'DCT'])->default('Automatic');
            $table->unsignedInteger('mileage')->default(0);
            $table->decimal('listing_price', 15, 2)->default(0);
            $table->string('color', 100)->nullable();
            $table->string('fuel_type', 50)->default('Bensin');
            $table->string('body_type', 60)->nullable();
            $table->string('engine_capacity', 50)->nullable();
            $table->string('vin_number', 100)->nullable();
            $table->string('engine_number', 100)->nullable();
            $table->string('ownership_type', 50)->default('Pribadi');
            $table->date('tax_expiry_date')->nullable();
            $table->string('document_status', 100)->default('STNK, BPKB & Faktur Lengkap');
            $table->string('photo_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
