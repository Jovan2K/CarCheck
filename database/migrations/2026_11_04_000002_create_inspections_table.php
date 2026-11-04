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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 40)->unique();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->date('inspection_date');
            $table->enum('status', ['In Progress', 'Completed'])->default('Completed');
            $table->enum('overall_risk_level', ['Low', 'Moderate', 'High'])->default('Low');
            $table->string('overall_grade', 5)->default('A');
            $table->boolean('flood_free')->default(true);
            $table->boolean('accident_free')->default(true);
            $table->decimal('total_repair_cost', 15, 2)->default(0);
            $table->text('general_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
