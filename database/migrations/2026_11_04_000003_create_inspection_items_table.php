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
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->enum('category', ['Mesin', 'Transmisi', 'Kaki-Kaki', 'Struktur Bodi', 'Interior', 'Pengereman']);
            $table->string('item_name', 150);
            $table->enum('condition', ['Good', 'Warning', 'Critical'])->default('Good');
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->text('defect_description')->nullable();
            $table->string('photo_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
