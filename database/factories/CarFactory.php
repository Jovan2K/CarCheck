<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vehicles = [
            ['brand' => 'Toyota', 'model' => 'Kijang Innova Zenix 2.0 G HV', 'body' => 'MPV', 'cc' => '2,000 cc', 'fuel' => 'Hybrid', 'price' => 415000000],
            ['brand' => 'Nissan', 'model' => 'X-Trail 2.5 T32 VL', 'body' => 'Micro/Minibus', 'cc' => '2,500 cc', 'fuel' => 'Bensin', 'price' => 265000000],
            ['brand' => 'Honda', 'model' => 'Civic RS 1.5 VTEC Turbo', 'body' => 'Sedan', 'cc' => '1,500 cc', 'fuel' => 'Bensin', 'price' => 385000000],
            ['brand' => 'Mitsubishi', 'model' => 'Pajero Sport Dakar 4x2', 'body' => 'SUV', 'cc' => '2,400 cc', 'fuel' => 'Diesel', 'price' => 440000000],
            ['brand' => 'Toyota', 'model' => 'Avanza Veloz 1.5 Q TSS', 'body' => 'MPV', 'cc' => '1,500 cc', 'fuel' => 'Bensin', 'price' => 235000000],
            ['brand' => 'Honda', 'model' => 'HR-V 1.5 SE CVT', 'body' => 'SUV', 'cc' => '1,500 cc', 'fuel' => 'Bensin', 'price' => 310000000],
        ];

        $choice = fake()->randomElement($vehicles);

        return [
            'user_id' => User::factory(),
            'brand' => $choice['brand'],
            'model' => $choice['model'],
            'year' => fake()->numberBetween(2018, 2025),
            'license_plate' => fake()->randomElement(['B', 'AD', 'AB', 'D', 'L']).' '.fake()->numberBetween(1000, 9999).' '.strtoupper(fake()->lexify('???')),
            'transmission' => fake()->randomElement(['Automatic', 'Manual', 'CVT']),
            'mileage' => fake()->numberBetween(12000, 95000),
            'listing_price' => $choice['price'],
            'color' => fake()->randomElement(['Putih/Hitam', 'Hitam Metalik', 'Silver/Hitam', 'Abu-abu Metalik']),
            'fuel_type' => $choice['fuel'],
            'body_type' => $choice['body'],
            'engine_capacity' => $choice['cc'],
            'vin_number' => strtoupper(fake()->bothify('MNTJBAT##L#######')),
            'engine_number' => strtoupper(fake()->bothify('QR########M')),
            'ownership_type' => 'Pribadi',
            'tax_expiry_date' => now()->addMonths(fake()->numberBetween(2, 11))->toDateString(),
            'document_status' => 'STNK, BPKB & Faktur Lengkap',
            'photo_url' => null,
        ];
    }
}
