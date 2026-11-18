# Panduan Login Akun CarCheck

Dokumen ini memuat daftar akun bawaan sistem CarCheck berdasarkan peran (*role*), kredensial login, dan fungsi operasional masing-masing peran.

---

## Ringkasan Kredensial Login

Semua akun menggunakan kata sandi default: **`password`**

| Peran (*Role*) | Nama Pengguna | Alamat Email | Kata Sandi | Deskripsi & Akses Utama |
| :--- | :--- | :--- | :--- | :--- |
| **Inspektur Independen** | Budi Santoso | `inspector@carcheck.id` | `password` | Membuat sesi inspeksi baru, mengisi checklist 32 titik temuan 6 area, input catatan teknis, dan verifikasi kelayakan kendaraan. |
| **Inspektur Independen** | Deni Kurniawan | `mechanic@carcheck.id` | `password` | Inspektur kedua untuk peninjauan teknis, evaluasi kondisi mesin & kaki-kaki, serta cetak laporan PDF resmi. |
| **Calon Pembeli** | Siti Rahmawati | `buyer@carcheck.id` | `password` | Konsumen/pembeli mobil bekas; melihat ringkasan skor risiko (A–E), estimasi biaya perbaikan, negosiasi harga, dan komparasi unit side-by-side. |
| **Showroom / Dealer** | Mitra Mobil Showroom | `showroom@carcheck.id` | `password` | Pemilik inventaris mobil; menambahkan data unit mobil (`/cars/create`), mengelola spesifikasi, dan membagikan URL publik laporan inspeksi. |

---

## Alur Penggunaan Berdasarkan Peran

### 1. Akun Inspektur Independen (`inspector@carcheck.id`)
- **Tujuan**: Melakukan pemeriksaan teknis unit mobil bekas secara komprehensif.
- **Fitur Utama**:
  - **Mulai Inspeksi Baru**: Akses menu **"Inspeksi Baru"** (`/inspections/create`), pilih unit mobil yang tersedia, dan tentukan tanggal inspeksi.
  - **Checklist 6 Area Komponen**: Menilai kondisi 32 titik komponen (Mesin, Transmisi, Kaki-Kaki, Sasis/Bodi, Interior/Elektrikal, Pengereman/Dokumen) dengan pilihan status `Good`, `Warning`, atau `Critical`.
  - **Estimasi Biaya & Foto Kerusakan**: Memasukkan perkiraan biaya restorasi komponen bermasalah dan mengunggah foto bukti fisik.
  - **Cetak Laporan Resmi**: Membuka lembar laporan siap cetak format A4 (`/inspections/{id}/print`).

### 2. Akun Calon Pembeli (`buyer@carcheck.id`)
- **Tujuan**: Menganalisis risiko pembelian dan membandingkan beberapa opsi mobil bekas sebelum transaksi.
- **Fitur Utama**:
  - **Dashboard Analitik**: Melihat ringkasan unit aman (*Low Risk*), perhatian (*Moderate Risk*), dan waspada (*High Risk*).
  - **Detail Laporan Standar Otospector**: Mengakses `/inspections/{id}` atau `/cars/{slug}/report` untuk memeriksa sertifikasi *Bebas Banjir*, *Bebas Tabrakan Besar*, serta rincian temuan komponen.
  - **Komparator Mobil**: Membuka `/compare` untuk membandingkan 2 atau 3 mobil secara berdampingan (harga penawaran, estimasi perbaikan, rasio risiko, dan grade keseluruhan).

### 3. Akun Showroom / Dealer (`showroom@carcheck.id`)
- **Tujuan**: Mendaftarkan inventaris unit mobil yang akan dijual dan menampilkan transparansi inspeksi kepada calon pembeli.
- **Fitur Utama**:
  - **Tambah Profil Mobil**: Akses `/cars/create` untuk mengisi identitas lengkap kendaraan (Merek, Model, Tahun, Nopol, Transmisi, Odometer, VIN/Rangka, No Mesin, Dokumen STNK/BPKB, dan Foto Unit).
  - **Kelola Data Unit**: Mengubah atau memperbarui informasi mobil yang terdaftar (`/cars/{id}/edit`).

---

## Pendaftaran Akun Baru

Selain menggunakan akun bawaan di atas, Anda juga dapat mendaftarkan akun baru melalui halaman **Registrasi** (`/register`):
1. Buka tautan `/register`.
2. Masukkan Nama Lengkap, Email, Nomor Telepon, Peran (*Inspektur Independen*, *Calon Pembeli*, atau *Showroom / Dealer*), dan Kata Sandi.
3. Setelah berhasil mendaftar, sistem akan langsung mengarahkan Anda ke Dashboard aplikasi.
