<?php

namespace Database\Factories;

use App\Models\Inspection;
use App\Models\InspectionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InspectionItem>
 */
class InspectionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inspection_id' => Inspection::factory(),
            'category' => fake()->randomElement(InspectionItem::CATEGORIES),
            'item_name' => fake()->randomElement([
                'Oli Mesin & Filter',
                'Kebocoran Kompresi / Head Gasket',
                'Perpindahan Gigi Transmisi',
                'Shock Absorber Depan & Belakang',
                'Tulang Sasis Utama, Crossmember & Apron',
                'Sistem AC, Kompresor & Temperatur Hembusan',
                'Tebal Kampas Rem Depan & Belakang',
            ]),
            'condition' => 'Good',
            'estimated_cost' => 0,
            'defect_description' => null,
            'photo_url' => null,
        ];
    }
}
