<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarCheckFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario 1: Dashboard utama menampilkan metrik ringkasan dan daftar inspeksi.
     */
    public function test_dashboard_page_displays_summary_metrics_and_inspections(): void
    {
        $this->seed();

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Total Sesi Inspeksi')
            ->assertSee('Rata-Rata Skor Kelayakan')
            ->assertSee('X-Trail 2.5 T32 VL')
            ->assertSee('170259096031');
    }

    /**
     * Skenario 2: Registrasi, Login, dan Logout pengguna berbasis session auth.
     */
    public function test_user_can_register_login_and_logout(): void
    {
        $registerResponse = $this->post(route('register.submit'), [
            'name' => 'Inspektur Uji',
            'email' => 'uji@carcheck.id',
            'phone' => '081299998888',
            'role' => 'Inspektur Independen',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $registerResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'uji@carcheck.id']);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $loginResponse = $this->post(route('login.submit'), [
            'email' => 'uji@carcheck.id',
            'password' => 'rahasia123',
        ]);

        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    /**
     * Skenario 3: CRUD Data Kendaraan (Create, Read, Update, Delete).
     */
    public function test_user_can_create_update_and_delete_car_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $storeResponse = $this->post(route('cars.store'), [
            'brand' => 'Toyota',
            'model' => 'Fortuner 2.8 VRZ GR Sport',
            'year' => 2023,
            'license_plate' => 'B 7788 GR',
            'transmission' => 'Automatic',
            'mileage' => 22000,
            'listing_price' => 520000000,
            'color' => 'Hitam Metalik',
            'fuel_type' => 'Diesel',
        ]);

        $car = Car::where('license_plate', 'B 7788 GR')->firstOrFail();
        $storeResponse->assertRedirect(route('cars.show', $car));

        $updateResponse = $this->put(route('cars.update', $car), [
            'brand' => 'Toyota',
            'model' => 'Fortuner 2.8 VRZ GR Sport 4x2',
            'year' => 2023,
            'license_plate' => 'B 7788 GR',
            'transmission' => 'Automatic',
            'mileage' => 24500,
            'listing_price' => 510000000,
        ]);

        $updateResponse->assertRedirect(route('cars.show', $car));
        $this->assertDatabaseHas('cars', [
            'id' => $car->id,
            'mileage' => 24500,
            'listing_price' => 510000000,
        ]);

        $deleteResponse = $this->delete(route('cars.destroy', $car));
        $deleteResponse->assertRedirect(route('cars.index'));
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }

    /**
     * Skenario 4 (Input Tidak Valid): Validasi server-side menolak data kendaraan yang tidak valid.
     */
    public function test_car_store_rejects_invalid_input_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->from(route('cars.create'))->post(route('cars.store'), [
            'brand' => '',
            'model' => '',
            'year' => 1950,
            'license_plate' => '',
            'transmission' => 'InvalidGearbox',
            'mileage' => -500,
            'listing_price' => 500,
        ]);

        $response->assertRedirect(route('cars.create'));
        $response->assertSessionHasErrors([
            'brand',
            'model',
            'year',
            'license_plate',
            'transmission',
            'mileage',
            'listing_price',
        ]);
    }

    /**
     * Skenario 5: Kalkulasi Otomatis Risk Index menetapkan 'Low' jika biaya perbaikan < 5% harga jual dan bebas cacat kritis.
     */
    public function test_risk_engine_assigns_low_risk_when_repair_cost_under_five_percent_and_no_critical_defects(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'listing_price' => 200000000, // Rp 200 Juta
        ]);

        // Total biaya Rp 4.000.000 (2% dari Rp 200 Juta -> < 5%)
        $response = $this->actingAs($user)->post(route('inspections.store'), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'general_notes' => 'Unit sehat, hanya perlu servis rutin.',
            'items' => [
                ['category' => 'Mesin', 'item_name' => 'Oli Mesin', 'condition' => 'Warning', 'estimated_cost' => 1000000, 'defect_description' => 'Oli kotor'],
                ['category' => 'Transmisi', 'item_name' => 'Perpindahan Gigi', 'condition' => 'Good', 'estimated_cost' => 0],
                ['category' => 'Kaki-Kaki', 'item_name' => 'Ban', 'condition' => 'Warning', 'estimated_cost' => 3000000, 'defect_description' => 'Ban depan aus'],
                ['category' => 'Struktur Bodi', 'item_name' => 'Tulang Sasis', 'condition' => 'Good', 'estimated_cost' => 0],
                ['category' => 'Interior', 'item_name' => 'Sistem AC', 'condition' => 'Good', 'estimated_cost' => 0],
                ['category' => 'Pengereman', 'item_name' => 'Kampas Rem', 'condition' => 'Good', 'estimated_cost' => 0],
            ],
        ]);

        $inspection = Inspection::latest('id')->firstOrFail();
        $response->assertRedirect(route('inspections.show', $inspection));

        $this->assertSame('Low', $inspection->overall_risk_level);
        $this->assertEquals(4000000.0, (float) $inspection->total_repair_cost);
        $this->assertTrue($inspection->accident_free);
        $this->assertTrue($inspection->flood_free);
    }

    /**
     * Skenario 6: Kalkulasi Otomatis Risk Index menetapkan 'Moderate' jika biaya perbaikan 5% - 15% harga jual.
     */
    public function test_risk_engine_assigns_moderate_risk_when_repair_cost_between_five_and_fifteen_percent(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'listing_price' => 200000000, // Rp 200 Juta
        ]);

        // Total biaya Rp 16.000.000 (8% dari Rp 200 Juta -> 5% s.d. 15%)
        $this->actingAs($user)->post(route('inspections.store'), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'items' => [
                ['category' => 'Mesin', 'item_name' => 'Mounting Mesin', 'condition' => 'Warning', 'estimated_cost' => 4000000],
                ['category' => 'Kaki-Kaki', 'item_name' => 'Shock Absorber & Ban', 'condition' => 'Warning', 'estimated_cost' => 8000000],
                ['category' => 'Interior', 'item_name' => 'Kompresor AC', 'condition' => 'Warning', 'estimated_cost' => 4000000],
                ['category' => 'Struktur Bodi', 'item_name' => 'Tulang Sasis', 'condition' => 'Good', 'estimated_cost' => 0],
            ],
        ]);

        $inspection = Inspection::latest('id')->firstOrFail();
        $this->assertSame('Moderate', $inspection->overall_risk_level);
        $this->assertSame('C', $inspection->overall_grade);
        $this->assertEquals(16000000.0, (float) $inspection->total_repair_cost);
    }

    /**
     * Skenario 7: Kalkulasi Otomatis Risk Index menetapkan 'High' jika biaya perbaikan > 15% harga jual.
     */
    public function test_risk_engine_assigns_high_risk_when_repair_cost_exceeds_fifteen_percent(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'listing_price' => 100000000, // Rp 100 Juta
        ]);

        // Total biaya Rp 20.000.000 (20% dari Rp 100 Juta -> > 15%)
        $this->actingAs($user)->post(route('inspections.store'), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'items' => [
                ['category' => 'Transmisi', 'item_name' => 'Overhaul Gearbox', 'condition' => 'Warning', 'estimated_cost' => 12000000],
                ['category' => 'Kaki-Kaki', 'item_name' => 'Rack Steer & Ban', 'condition' => 'Warning', 'estimated_cost' => 8000000],
            ],
        ]);

        $inspection = Inspection::latest('id')->firstOrFail();
        $this->assertSame('High', $inspection->overall_risk_level);
        $this->assertEquals(20000000.0, (float) $inspection->total_repair_cost);
    }

    /**
     * Skenario 8: Kalkulasi Otomatis Risk Index langsung menetapkan 'High' jika ada komponen Critical pada Struktur Bodi atau Mesin.
     */
    public function test_risk_engine_instantly_assigns_high_risk_when_struktur_bodi_or_mesin_is_critical(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'listing_price' => 500000000, // Rp 500 Juta
        ]);

        // Biaya hanya Rp 5.000.000 (1% dari harga jual), tetapi Struktur Bodi berstatus Critical
        $this->actingAs($user)->post(route('inspections.store'), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'items' => [
                [
                    'category' => 'Struktur Bodi',
                    'item_name' => 'Tulang Sasis Utama',
                    'condition' => 'Critical',
                    'estimated_cost' => 5000000,
                    'defect_description' => 'Sasis depan bengkok bekas tabrakan',
                ],
            ],
        ]);

        $inspection = Inspection::latest('id')->firstOrFail();
        $this->assertSame('High', $inspection->overall_risk_level);
        $this->assertFalse($inspection->accident_free);
    }

    /**
     * Skenario 9 (Input Tidak Valid): Validasi menolak sesi inspeksi dengan kondisi komponen tidak valid atau tanpa item.
     */
    public function test_inspection_store_rejects_invalid_condition_or_missing_items(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('inspections.store'), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'items' => [
                [
                    'category' => 'KategoriSalah',
                    'item_name' => 'Oli Mesin',
                    'condition' => 'RusakTidakValid',
                    'estimated_cost' => -100000,
                ],
            ],
        ]);

        $response->assertSessionHasErrors([
            'items.0.category',
            'items.0.condition',
            'items.0.estimated_cost',
        ]);
    }

    /**
     * Skenario 10: Halaman Detail Laporan dapat diakses melalui URI dinamis /inspections/{id} dan /cars/{slug}/report.
     */
    public function test_dynamic_uri_inspection_report_and_car_slug_report_are_accessible(): void
    {
        $this->seed();
        $inspection = Inspection::where('reference_number', '170259096031')->firstOrFail();
        $car = $inspection->car;

        $this->get(route('inspections.show', $inspection))
            ->assertOk()
            ->assertSee('170259096031')
            ->assertSee('MNTJBAT32L0029356')
            ->assertSee('Bebas Terendam Banjir');

        $this->get(route('cars.report', $car->slug))
            ->assertOk()
            ->assertSee('170259096031')
            ->assertSee($car->license_plate);
    }

    /**
     * Skenario 11: Pengguna dapat melakukan revisi data temuan inspeksi dan memicu kalkulasi ulang.
     */
    public function test_user_can_revise_inspection_findings_and_recalculate_risk(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id, 'listing_price' => 200000000]);
        $inspection = Inspection::factory()->create([
            'car_id' => $car->id,
            'inspector_id' => $user->id,
            'overall_risk_level' => 'Low',
            'total_repair_cost' => 0,
        ]);

        $response = $this->actingAs($user)->put(route('inspections.update', $inspection), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'general_notes' => 'Revisi ditemukan kerusakan mesin berat.',
            'items' => [
                [
                    'category' => 'Mesin',
                    'item_name' => 'Kebocoran Kompresi / Head Gasket',
                    'condition' => 'Critical',
                    'estimated_cost' => 15000000,
                    'defect_description' => 'Overheat dan kompresi bocor',
                ],
            ],
        ]);

        $response->assertRedirect(route('inspections.show', $inspection));
        $inspection->refresh();
        $this->assertSame('High', $inspection->overall_risk_level);
        $this->assertEquals(15000000.0, (float) $inspection->total_repair_cost);
    }

    /**
     * Skenario 12: Penambahan & penghapusan item temuan individual beserta unggah foto bukti berhasil mengalkulasi ulang risiko.
     */
    public function test_granular_inspection_item_crud_and_photo_upload_recalculates_inspection(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id, 'listing_price' => 200000000]);
        $inspection = Inspection::factory()->create([
            'car_id' => $car->id,
            'inspector_id' => $user->id,
            'overall_risk_level' => 'Low',
            'total_repair_cost' => 0,
        ]);

        $photo = UploadedFile::fake()->image('rem-cakram.jpg');

        $this->actingAs($user)->post(route('inspections.items.store', $inspection), [
            'category' => 'Pengereman',
            'item_name' => 'Disc Brake Depan Bergelombang',
            'condition' => 'Warning',
            'estimated_cost' => 15000000,
            'defect_description' => 'Piringan cakram bergelombang parah',
            'photo' => $photo,
        ])->assertRedirect(route('inspections.show', $inspection));

        $inspection->refresh();
        $item = $inspection->items()->firstOrFail();

        $this->assertSame('Moderate', $inspection->overall_risk_level);
        $this->assertEquals(15000000.0, (float) $inspection->total_repair_cost);
        $this->assertNotNull($item->photo_url);
        Storage::disk('public')->assertExists($item->photo_url);

        // Hapus item temuan dan pastikan biaya kembali ke 0 (Low Risk)
        $this->actingAs($user)
            ->delete(route('inspections.items.destroy', [$inspection, $item]))
            ->assertRedirect(route('inspections.show', $inspection));

        $inspection->refresh();
        $this->assertSame('Low', $inspection->overall_risk_level);
        $this->assertEquals(0.0, (float) $inspection->total_repair_cost);
    }

    /**
     * Skenario 13: Halaman Komparator Unit (Side-by-Side) dan Cetak PDF dapat diakses dengan benar.
     */
    public function test_side_by_side_comparator_and_printable_pdf_report_pages_load_correctly(): void
    {
        $this->seed();
        $inspections = Inspection::take(3)->pluck('id')->all();
        $firstInspection = Inspection::firstOrFail();

        $this->get(route('compare.index', ['ids' => $inspections]))
            ->assertOk()
            ->assertSee('Komparator Unit Mobil Bekas')
            ->assertSee('Rekomendasi Keputusan Pembelian Terbaik');

        $this->get(route('inspections.print', $firstInspection))
            ->assertOk()
            ->assertSee('Laporan Kondisi Kendaraan No. '.$firstInspection->reference_number);
    }

    /**
     * Skenario 14: Pengguna yang belum login (Guest) ditolak dan diarahkan ke login saat mengakses inspeksi.
     */
    public function test_unauthenticated_guest_cannot_access_or_submit_inspection(): void
    {
        $car = Car::factory()->create();

        $this->get(route('inspections.create'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->post(route('inspections.store'), [
            'car_id' => $car->id,
            'inspection_date' => '2026-11-04',
            'items' => [],
        ])->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    /**
     * Skenario 15: Pengguna dengan peran Calon Pembeli (End-Buyer) dilarang membuat atau mengedit inspeksi.
     */
    public function test_end_buyer_cannot_access_or_submit_inspection(): void
    {
        $buyer = User::factory()->buyer()->create();
        $car = Car::factory()->create();
        $inspection = Inspection::factory()->create(['car_id' => $car->id]);

        $this->actingAs($buyer)
            ->get(route('inspections.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($buyer)
            ->post(route('inspections.store'), [
                'car_id' => $car->id,
                'inspection_date' => '2026-11-04',
                'items' => [],
            ])->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($buyer)
            ->get(route('inspections.edit', $inspection))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($buyer)
            ->delete(route('inspections.destroy', $inspection))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    /**
     * Skenario 16: Pengguna dengan peran Inspektur Independen diizinkan mengakses form inspeksi.
     */
    public function test_independent_inspector_can_access_inspection_create_form(): void
    {
        $inspector = User::factory()->inspector()->create();
        Car::factory()->create();

        $this->actingAs($inspector)
            ->get(route('inspections.create'))
            ->assertOk()
            ->assertSee('Formulir Checklist 6 Area Komponen');
    }
}
