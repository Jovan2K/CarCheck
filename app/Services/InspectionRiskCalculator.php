<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\InspectionItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InspectionRiskCalculator
{
    /**
     * Standard multi-component checklist across all 6 functional areas (PRD 2.2.B + Otospector).
     *
     * @return array<string, array{title: string, subtitle: string, icon: string, items: list<array{name: string, hint: string, default_warning_cost: int, is_structural_or_heavy: bool}>}>
     */
    public static function defaultChecklist(): array
    {
        return [
            'Mesin' => [
                'title' => '1. Mesin (Engine Bay)',
                'subtitle' => 'Oli, kebocoran kompresi/radiator, belt, getaran mounting, performa start',
                'icon' => 'engine',
                'items' => [
                    [
                        'name' => 'Oli Mesin & Filter (Kualitas / Kebocoran Karter)',
                        'hint' => 'Periksa volume, warna oli mesin, lumpur (sludge), dan rembesan seal.',
                        'default_warning_cost' => 850000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Kebocoran Kompresi / Head Gasket',
                        'hint' => 'Deteksi gejala turun mesin, oli bercampur air, atau asap putih tebal.',
                        'default_warning_cost' => 6500000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Radiator, Water Pump & Sistem Pendingin',
                        'hint' => 'Periksa kondisi air radiator, selang, motor fan, dan indikasi overheat.',
                        'default_warning_cost' => 1750000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Drive Belt / Fan Belt & Tensioner',
                        'hint' => 'Periksa keretakan belt dan bunyi denging saat mesin awal hidup.',
                        'default_warning_cost' => 950000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Getaran Engine Mounting & Stabilitas Idle',
                        'hint' => 'Evaluasi getaran berlebih pada kabin saat idle dan kondisi karet mounting.',
                        'default_warning_cost' => 1800000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Performa Start, Dinamo Starter & Pengisian Accu',
                        'hint' => 'Cek voltase aki (charging test), respon cranking, dan alternator.',
                        'default_warning_cost' => 1250000,
                        'is_structural_or_heavy' => false,
                    ],
                ],
            ],
            'Transmisi' => [
                'title' => '2. Transmisi & Drivetrain',
                'subtitle' => 'Perpindahan gigi, respon transmisi, kopling/torque converter, driveshaft',
                'icon' => 'transmission',
                'items' => [
                    [
                        'name' => 'Perpindahan Gigi Transmisi (Manual / Matic)',
                        'hint' => 'Pastikan perpindahan gigi halus tanpa hentakan (jedug) atau selip.',
                        'default_warning_cost' => 3500000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Respon Transmisi & Kickdown Test Drive',
                        'hint' => 'Uji akselerasi, jeda perpindahan D ke R, dan responsivitas tarikan.',
                        'default_warning_cost' => 2500000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Kopling Set / Torque Converter & Flywheel',
                        'hint' => 'Periksa tinggi pedal kopling, getaran, atau gejala slip torque converter.',
                        'default_warning_cost' => 3200000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Driveshaft, CV Joint & Gardan',
                        'hint' => 'Periksa karet boot as roda, bunyi saat belok patah, dan dengung gardan.',
                        'default_warning_cost' => 1900000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Oli Transmisi & Kebocoran Seal Gearbox',
                        'hint' => 'Cek warna oli transmisi AT/MT dan rembesan pada housing transmisi.',
                        'default_warning_cost' => 1100000,
                        'is_structural_or_heavy' => false,
                    ],
                ],
            ],
            'Kaki-Kaki' => [
                'title' => '3. Kaki-Kaki & Suspensi (Underbody)',
                'subtitle' => 'Shock absorber, bushing arm, ball joint, tie rod, kondisi ban',
                'icon' => 'suspension',
                'items' => [
                    [
                        'name' => 'Shock Absorber Depan & Belakang',
                        'hint' => 'Cek kebocoran oli shockbreaker dan pantulan suspensi saat melewati jalan gelombang.',
                        'default_warning_cost' => 2400000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Bushing Lower Arm & Stabilizer Link',
                        'hint' => 'Periksa karet bushing retak/getas dan bunyi gluduk di kolong mobil.',
                        'default_warning_cost' => 1350000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Ball Joint, Tie Rod & Long Tie Rod',
                        'hint' => 'Periksa oblak pada roda depan dan kepresisian kemudi.',
                        'default_warning_cost' => 1500000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Kondisi 4 Ban (Ketebalan Alur, Tahun & Keretakan)',
                        'hint' => 'Cek keausan ban tidak rata, retak rambut pada dinding ban, dan kondisi velg alloy.',
                        'default_warning_cost' => 3200000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'EPS / Rack Steer & Keseimbangan Kemudi',
                        'hint' => 'Pastikan setir lurus (balance) saat test drive dan tidak ada bunyi ketukan rack steer.',
                        'default_warning_cost' => 2200000,
                        'is_structural_or_heavy' => false,
                    ],
                ],
            ],
            'Struktur Bodi' => [
                'title' => '4. Struktur Bodi & Eksterior',
                'subtitle' => 'Tulang sasis, indikasi benturan/tabrakan besar, panel sambungan, cat dan karat',
                'icon' => 'body',
                'items' => [
                    [
                        'name' => 'Tulang Sasis Utama, Crossmember & Apron',
                        'hint' => 'Komponen kritis: pastikan sasis presisi, bebas bekas las tarik atau bengkok.',
                        'default_warning_cost' => 8500000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Indikasi Benturan / Tabrakan Besar (Pilar A/B/C & Bullhead)',
                        'hint' => 'Periksa keutuhan titik las pabrikan (spot weld) dan sealer orisinal.',
                        'default_warning_cost' => 10000000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Panel Sambungan, Pintu, Kap Mesin & Pintu Bagasi',
                        'hint' => 'Cek kerapatan celah antar panel (gap), engsel pintu, fender, dan bemper.',
                        'default_warning_cost' => 1800000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Kondisi Cat, Ketebalan Dempul & Karat Bodi/Kolong',
                        'hint' => 'Deteksi perbedaan belang warna cat, baret, penyok, atau korosi pada lisplang.',
                        'default_warning_cost' => 2100000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Kaca Depan/Belakang, Kaca Film, Spion & Lampu Eksterior',
                        'hint' => 'Periksa jamur/baret kaca, retak lampu depan/belakang, dan pelipatan spion.',
                        'default_warning_cost' => 1200000,
                        'is_structural_or_heavy' => false,
                    ],
                ],
            ],
            'Interior' => [
                'title' => '5. Interior & Kelistrikan',
                'subtitle' => 'Panel dashboard, sistem AC, speedometer, audio, power window, bau apek (indikasi banjir)',
                'icon' => 'interior',
                'items' => [
                    [
                        'name' => 'Panel Dashboard, Setir & Instrumen Indikator',
                        'hint' => 'Pastikan indikator airbag, check engine, ABS normal dan tombol berfungsi.',
                        'default_warning_cost' => 1200000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Sistem AC, Kompresor & Temperatur Hembusan',
                        'hint' => 'Ukur suhu kisi AC (ideal < 10°C), cek blower, evaporator, dan sistem HVAC.',
                        'default_warning_cost' => 1850000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Speedometer, Odometer & Diagnosis Komputer (OBD DTC)',
                        'hint' => 'Verifikasi keaslian kilometer dengan keausan fisik dan hasil scan kode DTC.',
                        'default_warning_cost' => 1500000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Sistem Audio, Head Unit & Kamera Parkir',
                        'hint' => 'Periksa layar sentuh, speaker seluruh pintu, konektivitas Bluetooth, dan sensor.',
                        'default_warning_cost' => 950000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Power Window, Central Lock, Sabuk Pengaman & Jok',
                        'hint' => 'Uji seluruh kaca jendela otomatis, pengunci pintu, dan periksa karat di kolong jok.',
                        'default_warning_cost' => 1100000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Karpet Dasar & Bau Apek (Indikasi Rendaman Banjir)',
                        'hint' => 'Komponen kritis: cek lumpur kering di balik dashboard, rel jok berkarat, dan bau apek.',
                        'default_warning_cost' => 9500000,
                        'is_structural_or_heavy' => true,
                    ],
                ],
            ],
            'Pengereman' => [
                'title' => '6. Sistem Pengereman & Dokumen',
                'subtitle' => 'Tebal kampas, piringan rotor, rem parkir, fungsionalitas ABS & kelengkapan dokumen',
                'icon' => 'brakes',
                'items' => [
                    [
                        'name' => 'Tebal Kampas Rem Depan & Belakang (Brake Pad/Shoe)',
                        'hint' => 'Periksa sisa ketebalan kampas rem dan pastikan tidak berdecit saat mengerem.',
                        'default_warning_cost' => 950000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Piringan Rotor / Disc Brake (Permukaan & Keausan)',
                        'hint' => 'Periksa apakah piringan cakram bergelombang atau bergetar saat pengereman.',
                        'default_warning_cost' => 1650000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Rem Parkir / Handbrake & Kaliper Rem',
                        'hint' => 'Pastikan daya cengkeram rem tangan di tanjakan dan tidak ada macet pada piston kaliper.',
                        'default_warning_cost' => 750000,
                        'is_structural_or_heavy' => false,
                    ],
                    [
                        'name' => 'Fungsionalitas Modul ABS, Master Rem & Minyak Rem',
                        'hint' => 'Cek kadar air minyak rem, tekanan pedal rem, dan sensor kecepatan roda ABS.',
                        'default_warning_cost' => 2800000,
                        'is_structural_or_heavy' => true,
                    ],
                    [
                        'name' => 'Kelengkapan Dokumen (STNK, BPKB, Faktur) & Toolset',
                        'hint' => 'Cocokkan nomor rangka/mesin fisik dengan dokumen, cek kunci serep, ban serep & dongkrak.',
                        'default_warning_cost' => 1500000,
                        'is_structural_or_heavy' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Execute the CarCheck Backend Processing Engine (PRD Langkah 5 & Section 2.2.C).
     */
    public function recalculate(Inspection $inspection): Inspection
    {
        $inspection->load(['car', 'items']);
        $items = $inspection->items;

        // 1. Aggregate total repair cost: SUM(estimated_cost)
        $totalRepairCost = (float) $items->sum(function (InspectionItem $item): float {
            return $item->condition === 'Good' ? 0.0 : (float) $item->estimated_cost;
        });

        // 2. Check for Critical defects in Struktur Bodi (Chassis/Accident), Mesin (Heavy Engine), or Flood indicators
        $hasCriticalBody = $items->contains(function (InspectionItem $item): bool {
            return $item->category === 'Struktur Bodi' && $item->condition === 'Critical';
        });

        $hasCriticalEngine = $items->contains(function (InspectionItem $item): bool {
            return $item->category === 'Mesin' && $item->condition === 'Critical';
        });

        $hasCriticalFlood = $items->contains(function (InspectionItem $item): bool {
            if ($item->condition !== 'Critical') {
                return false;
            }

            $haystack = Str::lower($item->item_name.' '.$item->defect_description);

            return Str::contains($haystack, ['banjir', 'bau apek', 'rendaman', 'karpet dasar']);
        });

        $anyCritical = $items->contains(fn (InspectionItem $item): bool => $item->condition === 'Critical');
        $warningCount = $items->where('condition', 'Warning')->count();
        $criticalCount = $items->where('condition', 'Critical')->count();

        // 3. Calculate Repair Cost Ratio relative to Listing Price
        $listingPrice = (float) ($inspection->car?->listing_price ?? 0);
        $ratio = $listingPrice > 0 ? ($totalRepairCost / $listingPrice) : 0.0;

        // 4. Risk Index Algorithm (PRD 2.2.C & Flowchart Section 4)
        if ($hasCriticalBody || $hasCriticalEngine || $hasCriticalFlood || $ratio > 0.15) {
            $overallRiskLevel = 'High';
        } elseif ($ratio >= 0.05 || $anyCritical) {
            $overallRiskLevel = 'Moderate';
        } else {
            $overallRiskLevel = 'Low';
        }

        // 5. Flood-free & Accident-free verification badges
        $accidentFree = ! $hasCriticalBody;
        $floodFree = ! $hasCriticalFlood;

        // 6. Compute Overall Letter Grade (A, B, C, D, E) like Otospector report
        if ($overallRiskLevel === 'Low' && $criticalCount === 0 && $warningCount <= 1 && $ratio < 0.02) {
            $overallGrade = 'A';
        } elseif ($overallRiskLevel === 'Low') {
            $overallGrade = 'B';
        } elseif ($overallRiskLevel === 'Moderate') {
            $overallGrade = 'C';
        } elseif (! $accidentFree && ! $floodFree) {
            $overallGrade = 'E';
        } elseif ($criticalCount >= 3 || $ratio > 0.25) {
            $overallGrade = 'E';
        } else {
            $overallGrade = 'D';
        }

        $inspection->update([
            'total_repair_cost' => $totalRepairCost,
            'overall_risk_level' => $overallRiskLevel,
            'overall_grade' => $overallGrade,
            'accident_free' => $accidentFree,
            'flood_free' => $floodFree,
            'status' => 'Completed',
        ]);

        return $inspection->fresh(['car', 'inspector', 'items']);
    }

    /**
     * Build category-by-category breakdown and Otospector-style pillar grades.
     *
     * @return array<string, array{
     *     category: string,
     *     title: string,
     *     total_items: int,
     *     good_count: int,
     *     warning_count: int,
     *     critical_count: int,
     *     subtotal_cost: float,
     *     health_percentage: int,
     *     grade: string,
     *     status_label: string,
     *     items: Collection<int, InspectionItem>
     * }>
     */
    public function categoryBreakdown(Inspection $inspection): array
    {
        $inspection->loadMissing('items');
        $grouped = $inspection->items->groupBy('category');
        $defaults = self::defaultChecklist();
        $breakdown = [];

        foreach (InspectionItem::CATEGORIES as $category) {
            /** @var Collection<int, InspectionItem> $catItems */
            $catItems = $grouped->get($category, collect());
            $total = $catItems->count();
            $good = $catItems->where('condition', 'Good')->count();
            $warning = $catItems->where('condition', 'Warning')->count();
            $critical = $catItems->where('condition', 'Critical')->count();
            $subtotal = (float) $catItems->sum(fn (InspectionItem $i): float => $i->condition === 'Good' ? 0.0 : (float) $i->estimated_cost);

            if ($total > 0) {
                $points = ($good * 10) + ($warning * 5);
                $healthPct = (int) round(($points / ($total * 10)) * 100);
            } else {
                $healthPct = 100;
            }

            if ($critical >= 2 || $healthPct < 50) {
                $grade = 'E';
                $statusLabel = 'Buruk / Kritis';
            } elseif ($critical === 1 || $healthPct < 70) {
                $grade = 'D';
                $statusLabel = 'Perlu Perbaikan Berat';
            } elseif ($warning >= 2 || $healthPct < 85) {
                $grade = 'C';
                $statusLabel = 'Perlu Perhatian';
            } elseif ($warning === 1 || $healthPct < 96) {
                $grade = 'B';
                $statusLabel = 'Baik';
            } else {
                $grade = 'A';
                $statusLabel = 'Sangat Baik';
            }

            $breakdown[$category] = [
                'category' => $category,
                'title' => $defaults[$category]['title'] ?? $category,
                'total_items' => $total,
                'good_count' => $good,
                'warning_count' => $warning,
                'critical_count' => $critical,
                'subtotal_cost' => $subtotal,
                'health_percentage' => $healthPct,
                'grade' => $grade,
                'status_label' => $statusLabel,
                'items' => $catItems,
            ];
        }

        return $breakdown;
    }

    /**
     * Generate rule-based Smart Advisor insights & negotiation recommendations.
     *
     * @return array{
     *     verdict_title: string,
     *     verdict_summary: string,
     *     negotiation_advice: string,
     *     fair_price_min: float,
     *     fair_price_max: float,
     *     urgent_repairs: list<string>
     * }
     */
    public function generateAdvisorInsights(Inspection $inspection): array
    {
        $inspection->loadMissing(['car', 'items']);
        $listingPrice = (float) ($inspection->car?->listing_price ?? 0);
        $repairCost = (float) $inspection->total_repair_cost;
        $ratioPct = $inspection->repairRatioPercentage();
        $recommendedPrice = $inspection->recommendedNegotiationPrice();

        $urgentRepairs = $inspection->items
            ->whereIn('condition', ['Critical', 'Warning'])
            ->sortByDesc('estimated_cost')
            ->take(5)
            ->map(fn (InspectionItem $item): string => "[{$item->category}] {$item->item_name} — Rp ".number_format((float) $item->estimated_cost, 0, ',', '.'))
            ->values()
            ->all();

        if ($inspection->overall_risk_level === 'Low') {
            $verdictTitle = 'Direkomendasikan untuk Dibeli (Unit Layak Pakai)';
            $verdictSummary = "Kondisi struktural dan mekanikal utama sangat sehat. Rasio estimasi perbaikan hanya {$ratioPct}% dari harga penawaran (< 5%), bebas indikasi tabrakan besar maupun rendaman banjir.";
            $negotiationAdvice = 'Anda dapat menegosiasikan potongan sebesar biaya servis ringan/perbaikan langsung dari harga penawaran penjual.';
        } elseif ($inspection->overall_risk_level === 'Moderate') {
            $verdictTitle = 'Layak Dipertimbangkan dengan Negosiasi Harga';
            $verdictSummary = "Terdapat beberapa komponen aus pemakaian wajar (rasio perbaikan {$ratioPct}% dari harga jual). Tidak ditemukan cacat sasis fatal, namun perlu pencadangan dana restorasi pasca-pembelian.";
            $negotiationAdvice = 'Gunakan rincian daftar temuan komponen pada laporan ini untuk meminta pengurangan harga minimal sebesar total estimasi perbaikan ditambah margin risiko 2%.';
        } else {
            $verdictTitle = 'Risiko Tinggi — Waspada Sebelum Membeli!';
            $verdictSummary = "Ditemukan temuan kritis pada struktur bodi/sasis, mesin berat, indikasi banjir, atau rasio perbaikan mencapai {$ratioPct}% (> 15% harga jual). Biaya restorasi berpotensi membengkak.";
            $negotiationAdvice = 'Disarankan mencari unit alternatif yang lebih sehat, atau hanya lanjutkan transaksi apabila penjual bersedia menurunkan harga secara signifikan di bawah nilai pasar.';
        }

        return [
            'verdict_title' => $verdictTitle,
            'verdict_summary' => $verdictSummary,
            'negotiation_advice' => $negotiationAdvice,
            'fair_price_min' => max(0.0, $recommendedPrice * 0.97),
            'fair_price_max' => max(0.0, $listingPrice - $repairCost),
            'urgent_repairs' => $urgentRepairs,
        ];
    }
}
