<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inspection>
 */
class InspectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_number' => '170'.fake()->unique()->numerify('#########'),
            'car_id' => Car::factory(),
            'inspector_id' => User::factory(),
            'inspection_date' => now()->toDateString(),
            'status' => 'Completed',
            'overall_risk_level' => 'Low',
            'overall_grade' => 'B',
            'flood_free' => true,
            'accident_free' => true,
            'total_repair_cost' => 0,
            'general_notes' => 'Kondisi mobil baik, tidak ditemukan indikasi bekas tabrakan besar dan terendam banjir.',
        ];
    }
}
