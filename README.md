# E-RT RW Digital Bersatu (Laravel Version)

Aplikasi Web Terpadu Manajemen Administrasi, Keuangan, Kependudukan, dan Layanan Sosial RT/RW yang dimigrasi dari PHP Native ke framework **Laravel (Modern Fullstack)**.

---

## 📌 Informasi Proyek
- **Framework**: Laravel 12.x (v12.72.0, PHP 8.3+)
- **Database**: MySQL / MariaDB (Database: `keuangan_rt`)
- **Frontend**: Blade Templating, Bootstrap 5.3, Font Awesome 6.5, Chart.js, Plus Jakarta Sans font
- **Lokasi Proyek**: `C:\laragon\www\E-RW`
- **Repositori Asal**: [https://github.com/woszg1249/E-RT-RW-DIGITAL-Bersatu](https://github.com/woszg1249/E-RT-RW-DIGITAL-Bersatu)

---

## 🚀 Fitur Utama & Modul Sistem

### 1. 📊 Dashboard Interaktif
- Ringkasan Saldo Kas RT, Total Pemasukan vs Pengeluaran.
- Statistik total warga terdaftar & jumlah Kartu Keluarga (KK).
- Status tagihan iuran yang belum lunas & antrean pengajuan surat warga.
- Grafik visual arus kas bulanan menggunakan Chart.js.
- Agenda kegiatan terdekat & pintasan cepat layanan terpadu.

### 2. 💰 Buku Kas RT & Laporan Keuangan
- Catatan mutasi kas masuk dan keluar RT secara transparan.
- Upload foto struk / nota transaksi kas ke direktori bukti.
- Filter pencarian berdasarkan jenis, periode tanggal, dan kata kunci.
- **Laporan Arus Kas Bulanan**: Pratinjau cetak / ekspor PDF resmi lengkap dengan Kop Surat RT 06 / RW 19 Kelurahan Bekasi Jaya dan kolom tanda tangan pengurus.

### 3. 🧾 Manajemen Tagihan Iuran Warga
- Generate tagihan bulanan otomatis untuk seluruh warga atau per individu.
- Verifikasi pembayaran manual atau melalui simulasi QRIS digital.
- Otomatis mencatat kas masuk ke Buku Kas saat tagihan diverifikasi lunas.
- Riwayat transaksi pembayaran tagihan.

### 4. 👥 Kependudukan & Kartu Keluarga (KK)
- Direktori data warga dengan filter wilayah RT, NIK, No KK, status keluarga, dan pekerjaan.
- Manajemen Kartu Keluarga (KK) yang mengelompokkan kepala keluarga beserta anggotanya.
- **Cetak Kartu Keluarga Resmi**: Format cetak standar Dukcapil lengkap dengan dua tabel data pokok dan status hubungan keluarga.

### 5. ✉️ Layanan Surat Keterangan / Pengantar RT
- Warga dapat mengajukan surat keterangan secara online (Surat Pengantar RT/RW, Domisili, SKU, SKTM, dll).
- Admin dapat meninjau, menyetujui, atau menolak permohonan surat.
- **Cetak Surat Pengantar Resmi**: Format cetak standar pemerintahan dengan kop resmi RT 06 / RW 19, nomor surat otomatis, QR verifikasi, dan tanda tangan Ketua RT & Ketua RW.

### 6. 👶 Posyandu Balita & Anak
- Pencatatan penimbangan balita (Berat Badan, Tinggi Badan, Lingkar Kepala, dan status gizi/kesehatan).
- Riwayat tumbuh kembang anak warga.
- Cetak rekapitulasi data posyandu untuk laporan ke Puskesmas/Kelurahan.

### 7. ♻️ Bank Sampah Digital
- Pencatatan setoran sampah anorganik warga (Botol PET, Kardus, Kertas HVS, Kaleng, Minyak Jelantah).
- Kalkulasi otomatis total bobot (kg) dan konversi nilai rupiah.
- Verifikasi dan persetujuan setoran sampah oleh pengurus.

### 8. 🏪 Etalase UMKM Warga
- Katalog promosi produk dan jasa usaha mikro milik warga.
- Informasi alamat usaha, foto produk, jam operasional, dan tautan langsung ke WhatsApp pemilik usaha.

### 9. 🤝 Koperasi Simpan Pinjam & Integrasi IoT RFID
- Keanggotaan koperasi warga.
- Pengelolaan simpanan (setor & tarik saldo).
- Simulasi dan pengajuan pinjaman modal usaha ringan dengan kalkulator angsuran otomatis.
- Pencatatan pembayaran cicilan pinjaman.
- **IoT RFID Scanner Endpoint**: Endpoint `POST /api/iot/koperasi/scan` siap menerima data kartu RFID dari modul ESP32/RC522 untuk verifikasi identitas warga secara otomatis.

### 10. 📦 Inventaris & Fasilitas Aset RT
- Katalog fasilitas RT (tenda terop, kursi, sound system, proyektor).
- Sistem pengajuan peminjaman fasilitas secara daring oleh warga.
- Status persetujuan & pengembalian aset oleh pengurus RT.

### 11. 👤 Profil Pengguna & Keamanan
- Pembaruan biodata kependudukan (NIK, KK, status perkawinan, pekerjaan, dll).
- Upload foto profil dan penggantian password akun.
- Proteksi CSRF, password hashing dengan bcrypt, dan middleware otorisasi Admin vs Warga.

---

## 🛠️ Panduan Menjalankan Proyek

### Menggunakan Laragon Virtual Host:
Pastikan service Apache dan MySQL di Laragon dalam status **Running**, kemudian akses:
```
http://e-rw.test
```
Atau akses melalui path server:
```
http://localhost/E-RW/public/
```

### Menggunakan PHP Built-in Server:
Jalankan perintah berikut di PowerShell atau Command Prompt:
```powershell
cd C:\laragon\www\E-RW
php artisan serve
```
Akses di browser melalui: `http://127.0.0.1:8000`

---

## 🚀 Panduan Deployment ke Production / VPS

Saat aplikasi di-deploy ke server VPS:
1. **Clone & Setup Environment**:
   ```bash
   git clone https://github.com/woszg1249/E-RW.git
   cd E-RW
   cp .env.example .env
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   ```
2. **Konfigurasi Database & Domain**:
   Sesuaikan konfigurasi database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) dan `APP_URL` di file `.env`.
3. **Migrasi Database & Storage**:
   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```
4. **Optimasi Cache Laravel**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 🧪 Menjalankan Pengujian Otomatis (Tests)
```powershell
cd C:\laragon\www\E-RW
php artisan test
```
Semua rute, middleware, otentikasi role Warga & Admin, serta endpoint API telah lolos pengujian otomatis.
