# CarCheck — Sistem Inspeksi dan Evaluasi Mobil Bekas

**Kelompok 1 — Proyek Laravel Fundamental**
- **Ketua Kelompok**: Tedy Darmawan (`K3524037`)
- **Anggota**:
  - Galang Nur Seto (`K3524053`)
  - Afifah Zahrotul Khoir Sarifudin (`K3524019`)
  - Adelia Rosa Permata (`K3524001`)

---

## 1. Deskripsi Sistem

**CarCheck** adalah aplikasi web inspeksi dan evaluasi mobil bekas presisi tinggi yang memfasilitasi pencatatan kondisi multi-komponen (6 area fungsional), penilaian risiko otomatis (*Risk Index Algorithm*), dan kalkulasi biaya restorasi secara *real-time*. Sistem ini dirancang untuk mengatasi masalah asimetri informasi bagi:
1. **End-Buyer (Calon Pembeli Pribadi)**
2. **Inspektur Independen / Mekanik Lepas**
3. **Pedagang Mobil Bekas (Showroom)**

---

## 2. Fitur Utama & Pengayaan

### A. Autentikasi & Manajemen Data (CRUD)
- **Session Authentication**: Registrasi (`/register`), Login (`/login`), Logout (`/logout`), serta fitur **Login Instan Demo 1-Klik**.
- **CRUD Data Kendaraan (`/cars`)**: Mengelola profil dan identitas spesifik kendaraan (Merek, Model, Tahun, Nomor Polisi, Transmisi, Odometer, Harga Penawaran Awal, Nomor Rangka/VIN, Nomor Mesin, Warna, BBM, Pajak, dan Foto Kendaraan).
- **CRUD Sesi Inspeksi (`/inspections`)**: Membuat, membaca, merevisi/mengoreksi, dan menghapus lembar inspeksi kendaraan.
- **CRUD Item Temuan (`/inspections/{id}/items`)**: Menambah atau menghapus temuan komponen individual beserta unggahan foto bukti kerusakan.

### B. Formulir Inspeksi Multi-Komponen (6 Area Fungsional)
Menyediakan *checklist* 32 titik pemeriksaan standar pada 6 klaster area utama dengan status **Baik (`Good`)**, **Perlu Perhatian (`Warning`)**, dan **Rusak Parah (`Critical`)** beserta kalkulator *real-time*:
1. **Mesin (Engine Bay)**: Oli & filter, kebocoran kompresi/head gasket, radiator & sistem pendingin, drive/fan belt, engine mounting, performa start & pengisian aki.
2. **Transmisi & Drivetrain**: Perpindahan gigi, respon transmisi & kickdown, kopling/torque converter, driveshaft/CV joint & gardan, oli transmisi.
3. **Kaki-Kaki & Suspensi (Underbody)**: Shock absorber, bushing lower arm & stabilizer, ball joint & tie rod, kondisi 4 ban, rack steer & keseimbangan kemudi.
4. **Struktur Bodi & Eksterior**: Tulang sasis & apron, indikasi benturan/tabrakan besar, panel sambungan, kondisi cat/dempul/karat, kaca & lampu.
5. **Interior & Kelistrikan**: Panel dashboard & indikator, sistem AC & temperatur, speedometer & diagnosis OBD (DTC), audio & head unit, power window & jok, karpet dasar & bau apek (indikasi banjir).
6. **Sistem Pengereman & Dokumen**: Tebal kampas rem, piringan rotor/disc brake, rem parkir, modul ABS & minyak rem, serta kelengkapan dokumen (STNK, BPKB, Faktur).

### C. Kalkulasi Otomatis Skor Risiko (Processing Engine)
- **Agregasi Biaya**: Menghitung `total_repair_cost = SUM(estimated_cost)` dari seluruh item bermasalah.
- **Algoritma Risk Index (`overall_risk_level`)**:
  - **Rendah (`Low`)**: Bebas masalah sasis/mesin berat/banjir, dan rasio estimasi perbaikan `< 5%` dari harga penawaran.
  - **Sedang (`Moderate`)**: Masalah pemakaian wajar (kaki-kaki/servis rutin), rasio perbaikan `5% s.d. 15%` dari harga penawaran.
  - **Tinggi (`High`)**: Ditemukan status `Critical` pada area **Struktur Bodi (Sasis)**, **Mesin Berat**, **Indikasi Banjir**, atau rasio biaya perbaikan `> 15%` dari harga penawaran.
- **Predikat Skor Huruf (`A`, `B`, `C`, `D`, `E`) & Rekomendasi Harga Negosiasi Ideal**.

### D. Halaman Detail Laporan (URI Dinamis) & Pengayaan
- **URI Dinamis**: Dapat diakses melalui `/inspections/{id}` maupun `/cars/{slug}/report`.
- **Desain Laporan Standar Otospector**: Menampilkan tabel identitas lengkap, skor per area, skor keseluruhan (A–E), indikator *Bebas Terendam Banjir* & *Bebas Tabrakan Besar*, QR Code verifikasi, matriks checklist 6 area, dan dokumentasi temuan kerusakan.
- **Cetak / Ekspor PDF (`/inspections/{id}/print`)**: Halaman format cetak A4 resmi siap disimpan sebagai PDF.
- **Komparator Unit (`/compare`)**: Perbandingan berdampingan (*side-by-side comparison*) antara 2 hingga 3 mobil untuk menentukan rekomendasi unit terbaik.
- **Unggah Foto Bukti Kerusakan**: Penyimpanan dokumentasi visual komponen rusak ke `storage/app/public`.

---

## 3. Struktur Database (ERD)

1. **`users`**: `id`, `name`, `email`, `phone`, `role`, `password`, `timestamps`
2. **`cars`**: `id`, `user_id` (FK), `brand`, `model`, `slug`, `year`, `license_plate`, `transmission`, `mileage`, `listing_price`, `color`, `fuel_type`, `body_type`, `engine_capacity`, `vin_number`, `engine_number`, `ownership_type`, `tax_expiry_date`, `document_status`, `photo_url`, `timestamps`
3. **`inspections`**: `id`, `reference_number`, `car_id` (FK), `inspector_id` (FK), `inspection_date`, `status`, `overall_risk_level`, `overall_grade`, `flood_free`, `accident_free`, `total_repair_cost`, `general_notes`, `timestamps`
4. **`inspection_items`**: `id`, `inspection_id` (FK Cascade Delete), `category`, `item_name`, `condition`, `estimated_cost`, `defect_description`, `photo_url`, `timestamps`

---

## 4. Panduan Instalasi & Menjalankan Aplikasi

```bash
# 1. Install dependensi PHP & JavaScript
composer install
npm install

# 2. Salin konfigurasi environment & generate key (jika belum ada)
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi beserta seeder data contoh (termasuk laporan Nissan X-Trail #170259096031)
php artisan migrate:fresh --seed

# 4. Buat symlink storage publik & build aset frontend
php artisan storage:link
npm run build

# 5. Jalankan server lokal
php artisan serve
```

### Akun Demo (Password: `password`)
- `tedy@carcheck.id` — Tedy Darmawan (Inspektur Independen)
- `galang@carcheck.id` — Galang Nur Seto (Inspektur Independen)
- `afifah@carcheck.id` — Afifah Zahrotul Khoir Sarifudin (Calon Pembeli)
- `adelia@carcheck.id` — Adelia Rosa Permata (Showroom / Dealer)

---

## 5. Hasil Pengujian (Testing)

Aplikasi dilengkapi **15 skenario pengujian otomatis (PHPUnit)** pada `tests/Feature/CarCheckFeatureTest.php` dan `tests/Feature/ExampleTest.php`, mencakup pengujian alur fungsional, cabang algoritma Risk Index (`Low`, `Moderate`, `High`, serta temuan sasis kritis), URI dinamis, komparator, unggah foto, dan validasi input tidak valid:

```bash
php artisan test --compact
```
- **Total Pengujian**: 15 *passed* (78 *assertions*)
