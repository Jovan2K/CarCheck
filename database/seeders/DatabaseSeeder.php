<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Models\User;
use App\Services\InspectionRiskCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $calculator = new InspectionRiskCalculator;

        // 1. Seed Kelompok 1 CarCheck Team Users & Default Inspector
        $tedy = User::updateOrCreate(
            ['email' => 'tedy@carcheck.id'],
            [
                'name' => 'Tedy Darmawan (K3524037)',
                'phone' => '081234567037',
                'role' => 'Inspektur Independen',
                'password' => Hash::make('password'),
            ]
        );

        $galang = User::updateOrCreate(
            ['email' => 'galang@carcheck.id'],
            [
                'name' => 'Galang Nur Seto (K3524053)',
                'phone' => '081234567053',
                'role' => 'Inspektur Independen',
                'password' => Hash::make('password'),
            ]
        );

        $afifah = User::updateOrCreate(
            ['email' => 'afifah@carcheck.id'],
            [
                'name' => 'Afifah Zahrotul Khoir Sarifudin (K3524019)',
                'phone' => '081234567019',
                'role' => 'Calon Pembeli',
                'password' => Hash::make('password'),
            ]
        );

        $adelia = User::updateOrCreate(
            ['email' => 'adelia@carcheck.id'],
            [
                'name' => 'Adelia Rosa Permata (K3524001)',
                'phone' => '081234567001',
                'role' => 'Showroom / Dealer',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Unit 1: Nissan X-Trail 2.5 T32 (2020) — Exact match with Contoh Laporan Otospector PDF
        $nissanXtrail = Car::updateOrCreate(
            ['license_plate' => 'B 2590 XTR'],
            [
                'user_id' => $tedy->id,
                'brand' => 'Nissan',
                'model' => 'X-Trail 2.5 T32 VL',
                'slug' => 'nissan-x-trail-25-t32-2020',
                'year' => 2020,
                'transmission' => 'Automatic',
                'mileage' => 47355,
                'listing_price' => 275000000,
                'color' => 'Putih/Hitam',
                'fuel_type' => 'Bensin',
                'body_type' => 'Micro/Minibus (SUV)',
                'engine_capacity' => '2,500 cc',
                'vin_number' => 'MNTJBAT32L0029356',
                'engine_number' => 'QR25491820M',
                'ownership_type' => 'Pribadi (Tangan Kedua)',
                'tax_expiry_date' => '2027-04-22',
                'document_status' => 'STNK, BPKB & Faktur Lengkap (Fisik Sesuai)',
            ]
        );

        $inspXtrail = Inspection::updateOrCreate(
            ['reference_number' => '170259096031'],
            [
                'car_id' => $nissanXtrail->id,
                'inspector_id' => $tedy->id,
                'inspection_date' => '2026-10-16',
                'status' => 'Completed',
                'general_notes' => 'Kondisi mobil baik, tidak ditemukan indikasi bekas tabrakan besar dan terendam banjir. Ada beberapa kekurangan pemakaian wajar (oli mesin kotor, fan belt aus, ban mulai retak, piringan cakram bergelombang, dan DTC pada ABS/HVAC). Test drive dilakukan di jalanan rata dengan kecepatan di bawah 30 km/jam.',
            ]
        );

        $this->seedChecklistWithOverrides($inspXtrail, [
            'Oli Mesin & Filter (Kualitas / Kebocoran Karter)' => [
                'condition' => 'Warning',
                'estimated_cost' => 850000,
                'defect_description' => 'Oli mesin kotor dan perlu segera dilakukan penggantian oli beserta filter.',
            ],
            'Drive Belt / Fan Belt & Tensioner' => [
                'condition' => 'Warning',
                'estimated_cost' => 950000,
                'defect_description' => 'Belt aus | Suara mesin ada bunyi denging area fan belt saat awal hidup.',
            ],
            'Kondisi 4 Ban (Ketebalan Alur, Tahun & Keretakan)' => [
                'condition' => 'Warning',
                'estimated_cost' => 2800000,
                'defect_description' => 'Ban 4 roda mulai retak dan alur ban mulai tidak rata.',
            ],
            'Speedometer, Odometer & Diagnosis Komputer (OBD DTC)' => [
                'condition' => 'Warning',
                'estimated_cost' => 1200000,
                'defect_description' => 'Diagnosis komputer ada kode DTC di sistem ABS (U1000 CAN comm) dan HVAC (B2578 Sunload sensor).',
            ],
            'Power Window, Central Lock, Sabuk Pengaman & Jok' => [
                'condition' => 'Warning',
                'estimated_cost' => 650000,
                'defect_description' => 'Rangka bawah jok kanan dan kiri depan ada karat permukaan.',
            ],
            'Piringan Rotor / Disc Brake (Permukaan & Keausan)' => [
                'condition' => 'Warning',
                'estimated_cost' => 1450000,
                'defect_description' => 'Disc brake depan dan belakang R/L bergelombang, perlu bubut cakram.',
            ],
        ]);
        $calculator->recalculate($inspXtrail);

        // 3. Unit 2: Toyota Kijang Innova Zenix 2.0 V Hybrid (2023) — Low Risk, Grade A
        $zenix = Car::updateOrCreate(
            ['license_plate' => 'AD 1028 ZNX'],
            [
                'user_id' => $afifah->id,
                'brand' => 'Toyota',
                'model' => 'Kijang Innova Zenix 2.0 V HV Modellista',
                'slug' => 'toyota-kijang-innova-zenix-2023',
                'year' => 2023,
                'transmission' => 'CVT',
                'mileage' => 18400,
                'listing_price' => 465000000,
                'color' => 'Hitam Metalik / Hitam',
                'fuel_type' => 'Hybrid',
                'body_type' => 'MPV',
                'engine_capacity' => '1,987 cc',
                'vin_number' => 'MHFAB12G9P0081124',
                'engine_number' => 'M20A9918231',
                'ownership_type' => 'Pribadi (Tangan Pertama)',
                'tax_expiry_date' => '2027-08-14',
                'document_status' => 'STNK, BPKB, Faktur & Buku Servis Resmi Lengkap',
            ]
        );

        $inspZenix = Inspection::updateOrCreate(
            ['reference_number' => '170259098102'],
            [
                'car_id' => $zenix->id,
                'inspector_id' => $galang->id,
                'inspection_date' => '2026-10-25',
                'status' => 'Completed',
                'general_notes' => 'Unit istimewa seperti baru, record servis resmi Auto2000 lengkap, baterai hybrid sehat 100%, bebas tabrakan dan bebas banjir. Hanya perlu spooring ringan dan penggantian wiper blade.',
            ]
        );

        $this->seedChecklistWithOverrides($inspZenix, [
            'Kaca Depan/Belakang, Kaca Film, Spion & Lampu Eksterior' => [
                'condition' => 'Warning',
                'estimated_cost' => 450000,
                'defect_description' => 'Karet wiper depan mulai mengeras, disarankan ganti karet wiper.',
            ],
        ]);
        $calculator->recalculate($inspZenix);

        // 4. Unit 3: Honda Civic RS 1.5 VTEC Turbo (2020) — Moderate Risk (5% - 15% ratio), Grade C
        $civic = Car::updateOrCreate(
            ['license_plate' => 'B 1998 CVC'],
            [
                'user_id' => $adelia->id,
                'brand' => 'Honda',
                'model' => 'Civic RS 1.5 VTEC Turbo',
                'slug' => 'honda-civic-rs-15-turbo-2020',
                'year' => 2020,
                'transmission' => 'CVT',
                'mileage' => 68900,
                'listing_price' => 340000000,
                'color' => 'Merah Rallye / Hitam Kulit',
                'fuel_type' => 'Bensin',
                'body_type' => 'Sedan',
                'engine_capacity' => '1,498 cc',
                'vin_number' => 'MRHFC1680LP019283',
                'engine_number' => 'L15B72019482',
                'ownership_type' => 'Pribadi',
                'tax_expiry_date' => '2027-02-10',
                'document_status' => 'STNK, BPKB & Faktur Lengkap',
            ]
        );

        $inspCivic = Inspection::updateOrCreate(
            ['reference_number' => '170259104419'],
            [
                'car_id' => $civic->id,
                'inspector_id' => $tedy->id,
                'inspection_date' => '2026-10-29',
                'status' => 'Completed',
                'general_notes' => 'Struktur sasis utuh dan mesin turbo responsif, namun sektor kaki-kaki depan (rack steer, shockbreaker, link stabilizer), 4 ban, serta motor fan AC memerlukan peremajaan segera. Estimasi perbaikan berada di rentang 7.5% dari harga penawaran (Moderate Risk).',
            ]
        );

        $this->seedChecklistWithOverrides($inspCivic, [
            'Getaran Engine Mounting & Stabilitas Idle' => [
                'condition' => 'Warning',
                'estimated_cost' => 3200000,
                'defect_description' => 'Engine mounting kanan hidrolis mulai kempes, terasa getaran halus saat posisi D tahan rem.',
            ],
            'Shock Absorber Depan & Belakang' => [
                'condition' => 'Warning',
                'estimated_cost' => 4800000,
                'defect_description' => 'Shock absorber depan kanan-kiri rembes oli dan bantingan mulai mengayun.',
            ],
            'EPS / Rack Steer & Keseimbangan Kemudi' => [
                'condition' => 'Warning',
                'estimated_cost' => 4500000,
                'defect_description' => 'Bushing rack steer khas Civic Turbo mulai bunyi gluduk saat lewat jalan keriting.',
            ],
            'Kondisi 4 Ban (Ketebalan Alur, Tahun & Keretakan)' => [
                'condition' => 'Warning',
                'estimated_cost' => 6400000,
                'defect_description' => 'Keempat ban produksi 2020 sudah tipis mendekati TWI, wajib ganti 4 ban baru.',
            ],
            'Sistem AC, Kompresor & Temperatur Hembusan' => [
                'condition' => 'Warning',
                'estimated_cost' => 3500000,
                'defect_description' => 'Evaporator AC kotor dan magnetic clutch kompresor mulai lemah saat siang terik.',
            ],
            'Tebal Kampas Rem Depan & Belakang (Brake Pad/Shoe)' => [
                'condition' => 'Warning',
                'estimated_cost' => 1850000,
                'defect_description' => 'Kampas rem depan sisa 25%, disarankan ganti kampas rem depan-belakang.',
            ],
            'Kondisi Cat, Ketebalan Dempul & Karat Bodi/Kolong' => [
                'condition' => 'Warning',
                'estimated_cost' => 1600000,
                'defect_description' => 'Bemper depan bawah baret trotoar dan fender kiri ada bekas repaint tipis.',
            ],
        ]);
        $calculator->recalculate($inspCivic);

        // 5. Unit 4: Mitsubishi Pajero Sport Dakar 4x2 (2018) — High Risk, Grade D (Structural & Heavy Engine Defect)
        $pajero = Car::updateOrCreate(
            ['license_plate' => 'B 8821 PJR'],
            [
                'user_id' => $adelia->id,
                'brand' => 'Mitsubishi',
                'model' => 'Pajero Sport 2.4 Dakar Ultimate',
                'slug' => 'mitsubishi-pajero-sport-dakar-2018',
                'year' => 2018,
                'transmission' => 'Automatic',
                'mileage' => 118500,
                'listing_price' => 375000000,
                'color' => 'Putih Mutiara / Beige',
                'fuel_type' => 'Diesel',
                'body_type' => 'SUV',
                'engine_capacity' => '2,442 cc',
                'vin_number' => 'MMBGUKR10JH055192',
                'engine_number' => '4N15Uac8812',
                'ownership_type' => 'Perusahaan',
                'tax_expiry_date' => '2026-11-30',
                'document_status' => 'STNK & BPKB Ada (Pajak Hampir Habis)',
            ]
        );

        $inspPajero = Inspection::updateOrCreate(
            ['reference_number' => '170259112890'],
            [
                'car_id' => $pajero->id,
                'inspector_id' => $galang->id,
                'inspection_date' => '2026-11-02',
                'status' => 'Completed',
                'general_notes' => 'PERINGATAN RISIKO TINGGI: Ditemukan bekas perbaikan tabrakan depan pada bullhead/apron radiator serta rembesan oli turbo & kebocoran kompresi mesin. Transmisi matic juga mengalami gejala jedug saat perpindahan gigi 2 ke 3.',
            ]
        );

        $this->seedChecklistWithOverrides($inspPajero, [
            'Kebocoran Kompresi / Head Gasket' => [
                'condition' => 'Critical',
                'estimated_cost' => 18500000,
                'defect_description' => 'Blow-by gas tinggi dari dipstick oli dan rembesan oli berat pada intercooler turbocharger.',
            ],
            'Radiator, Water Pump & Sistem Pendingin' => [
                'condition' => 'Warning',
                'estimated_cost' => 3800000,
                'defect_description' => 'Radiator pernah diganti non-genuine dan visco fan lemah.',
            ],
            'Perpindahan Gigi Transmisi (Manual / Matic)' => [
                'condition' => 'Critical',
                'estimated_cost' => 14500000,
                'defect_description' => 'Gejala hentakan keras (delay & jedug) saat masuk R dan perpindahan gigi rendah, perlu overhaul valve body.',
            ],
            'Tulang Sasis Utama, Crossmember & Apron' => [
                'condition' => 'Critical',
                'estimated_cost' => 16000000,
                'defect_description' => 'Apron depan kiri dan crossmember radiator terdapat bekas ketok las tarik akibat benturan depan.',
            ],
            'Indikasi Benturan / Tabrakan Besar (Pilar A/B/C & Bullhead)' => [
                'condition' => 'Critical',
                'estimated_cost' => 12000000,
                'defect_description' => 'Titik spot weld pabrikan pada bullhead depan sudah tidak orisinal dan kap mesin dempul tebal (>900 mikron).',
            ],
            'Bushing Lower Arm & Stabilizer Link' => [
                'condition' => 'Warning',
                'estimated_cost' => 2900000,
                'defect_description' => 'Bushing lower arm depan pecah dan ball joint mulai oblak.',
            ],
        ]);
        $calculator->recalculate($inspPajero);
    }

    /**
     * Populate all 32 default checklist items across the 6 areas for an inspection, applying specific defect overrides.
     *
     * @param  array<string, array{condition: string, estimated_cost: int|float, defect_description: string}>  $overrides
     */
    private function seedChecklistWithOverrides(Inspection $inspection, array $overrides): void
    {
        $inspection->items()->delete();
        $checklist = InspectionRiskCalculator::defaultChecklist();

        foreach ($checklist as $category => $group) {
            foreach ($group['items'] as $preset) {
                $itemName = $preset['name'];
                $override = $overrides[$itemName] ?? null;

                InspectionItem::create([
                    'inspection_id' => $inspection->id,
                    'category' => $category,
                    'item_name' => $itemName,
                    'condition' => $override['condition'] ?? 'Good',
                    'estimated_cost' => $override['estimated_cost'] ?? 0,
                    'defect_description' => $override['defect_description'] ?? null,
                ]);
            }
        }
    }
}
