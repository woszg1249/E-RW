<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\BankSampah;
use App\Models\Kegiatan;
use App\Models\Keuangan;
use App\Models\Posyandu;
use App\Models\SuratPengajuan;
use App\Models\Tagihan;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Jalankan seeder test data lengkap untuk website E-RW.
     */
    public function run(): void
    {
        $admin = User::where('role', 'Admin')->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        // 0. Akun Warga Baru Contoh (Budi Santoso)
        $wargaBaru = User::firstOrCreate(
            ['username' => 'budi.santoso'],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('Password123'),
                'nik' => '3275010101950001',
                'no_kk' => '3275010101950001',
                'alamat' => 'Jl. Mawar Indah No. 15 RT 06/RW 19',
                'kelamin' => 'Laki-laki',
                'no_hp' => '081234567899',
                'rt' => '06',
                'rw' => '19',
                'role' => 'Warga',
                'foto' => 'profile.jpg',
                'status_keluarga' => 'Kepala Keluarga',
                'pekerjaan' => 'Karyawan Swasta',
                'status_perkawinan' => 'Kawin',
                'pendidikan' => 'S1 / Sarjana',
                'agama' => 'Islam',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '1995-05-15',
            ]
        );

        $wargaList = User::where('role', 'Warga')->get();
        if ($wargaList->isEmpty()) {
            $wargaList = User::all();
        }

        $warga1 = $wargaList->first();
        $warga2 = $wargaList->count() > 1 ? $wargaList->skip(1)->first() : $warga1;
        $warga3 = $wargaList->count() > 2 ? $wargaList->skip(2)->first() : $warga1;

        // 1. Seed Kegiatan Warga
        $kegiatanData = [
            [
                'judul' => 'Kerja Bakti Lingkungan & Fogging Nyamuk DBD',
                'deskripsi' => 'Kerja bakti bersama seluruh warga membersihkan saluran air, selokan, dan pengasapan (fogging) nyamuk DBD menjelang musim hujan.',
                'tanggal_mulai' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'waktu' => '07:00 - 11:00 WIB',
                'lokasi' => 'Balai Warga RT 06 & Lingkungan RW 19',
                'dibuat_oleh' => $adminId,
                'tanggal_dibuat' => Carbon::now(),
            ],
            [
                'judul' => 'Senam Sehat Lansia & Cek Kesehatan Gratis',
                'deskripsi' => 'Senam kebugaran jasmani bersama kader kesehatan, dilanjutkan cek tekanan darah dan gula darah gratis untuk warga lansia.',
                'tanggal_mulai' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'waktu' => '06:30 - 09:30 WIB',
                'lokasi' => 'Lapangan Olahraga RW 19',
                'dibuat_oleh' => $adminId,
                'tanggal_dibuat' => Carbon::now(),
            ],
            [
                'judul' => 'Rapat Koordinasi Pengurus RT & Siskamling',
                'deskripsi' => 'Evaluasi jadwal ronda malam warga, koordinasi sistem portal digital RFID, dan peremajaan lampu penerangan jalan.',
                'tanggal_mulai' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'waktu' => '19:30 - 22:00 WIB',
                'lokasi' => 'Pos Ronda Utama RT 06',
                'dibuat_oleh' => $adminId,
                'tanggal_dibuat' => Carbon::now(),
            ],
        ];

        foreach ($kegiatanData as $keg) {
            Kegiatan::firstOrCreate(['judul' => $keg['judul']], $keg);
        }

        // 2. Seed Posyandu Balita
        if ($warga1) {
            $posyanduData = [
                [
                    'id_warga' => $warga1->id,
                    'nama_bayi' => 'Muhammad Al Fatih',
                    'usia_bulan' => 14,
                    'berat_badan' => 10.4,
                    'tinggi_badan' => 78.5,
                    'lingkar_kepala' => 46.2,
                    'status_kesehatan' => 'Gizi Baik (Normal)',
                    'catatan' => 'Pertumbuhan sangat baik, telah menerima imunisasi PCV lanjutan.',
                    'tanggal_pemeriksaan' => Carbon::now()->subDays(2),
                    'pemeriksaan_oleh' => 'Bidan Siti Nurhaliza',
                    'jenis_kelamin' => 'Laki-laki',
                ],
                [
                    'id_warga' => $warga2 ? $warga2->id : $warga1->id,
                    'nama_bayi' => 'Aisyah Putri Azzahra',
                    'usia_bulan' => 9,
                    'berat_badan' => 8.2,
                    'tinggi_badan' => 70.0,
                    'lingkar_kepala' => 43.8,
                    'status_kesehatan' => 'Gizi Baik (Normal)',
                    'catatan' => 'Motorik aktif, anjuran MPASI bergizi kaya zat besi dan protein telur/ikan.',
                    'tanggal_pemeriksaan' => Carbon::now()->subDays(5),
                    'pemeriksaan_oleh' => 'Kader Ibu Rahmawati',
                    'jenis_kelamin' => 'Perempuan',
                ],
                [
                    'id_warga' => $warga3 ? $warga3->id : $warga1->id,
                    'nama_bayi' => 'Rayyan Danendra',
                    'usia_bulan' => 22,
                    'berat_badan' => 12.1,
                    'tinggi_badan' => 85.0,
                    'lingkar_kepala' => 48.0,
                    'status_kesehatan' => 'Gizi Sangat Baik',
                    'catatan' => 'Tumbuh kembang prima, vitamin A dosis biru telah diberikan.',
                    'tanggal_pemeriksaan' => Carbon::now()->subDays(12),
                    'pemeriksaan_oleh' => 'Bidan Siti Nurhaliza',
                    'jenis_kelamin' => 'Laki-laki',
                ],
            ];

            foreach ($posyanduData as $pos) {
                Posyandu::firstOrCreate(['nama_bayi' => $pos['nama_bayi']], $pos);
            }
        }

        // 3. Seed UMKM Warga
        $umkmData = [
            [
                'nama_usaha' => 'Warung Nasi Uduk & Lontong Sayur Bu Sri',
                'kategori' => 'Kuliner & Makanan',
                'deskripsi' => 'Menyediakan nasi uduk komplit, lontong sayur padang, aneka gorengan hangat, dan teh manis segar setiap pagi.',
                'alamat' => 'Jl. Mawar No. 04 RT 06/RW 19',
                'jam_operasional' => '06:00 - 11:00 WIB',
                'whatsapp' => '081234567891',
                'foto' => 'umkm_kuliner.jpg',
                'produk_layanan' => 'Nasi Uduk Komplit, Lontong Sayur, Bakwan, Tempe Mendoan, Tahu Isi',
                'estimasi_jarak' => '100 meter dari Balai RT',
                'google_maps_link' => 'https://maps.google.com/?q=Bekasi+Jaya',
                'status' => 'Aktif',
            ],
            [
                'nama_usaha' => 'Berkah Laundry Kilat & Dry Clean',
                'kategori' => 'Jasa Cuci & Pakaian',
                'deskripsi' => 'Jasa cuci kiloan, satuan, bed cover, dan dry clean dengan pewangi premium dan setrika uap rapi.',
                'alamat' => 'Jl. Kenanga Raya No. 18 RT 06/RW 19',
                'jam_operasional' => '08:00 - 20:00 WIB',
                'whatsapp' => '081298765411',
                'foto' => 'umkm_laundry.jpg',
                'produk_layanan' => 'Cuci Kiloan Rp 7.000/kg, Cuci Bed Cover, Setrika Kilat 4 Jam',
                'estimasi_jarak' => '250 meter dari Pos Ronda',
                'google_maps_link' => 'https://maps.google.com/?q=Bekasi+Jaya',
                'status' => 'Aktif',
            ],
            [
                'nama_usaha' => 'Toko Kelontong Berkah Pak Dimas',
                'kategori' => 'Kebutuhan Pokok & Sembako',
                'deskripsi' => 'Menjual beras pulen, minyak goreng, telur ayam segar, gas elpiji 3kg, dan air galon isi ulang.',
                'alamat' => 'Jl. Anggrek No. 02 RT 07/RW 19',
                'jam_operasional' => '07:00 - 21:00 WIB',
                'whatsapp' => '081399881122',
                'foto' => 'umkm_sembako.jpg',
                'produk_layanan' => 'Beras Pandan Wangi, Minyak Kita, Gas 3Kg, Aqua Galon, Token Listrik',
                'estimasi_jarak' => '50 meter dari Gapura RT 07',
                'google_maps_link' => 'https://maps.google.com/?q=Bekasi+Jaya',
                'status' => 'Aktif',
            ],
        ];

        foreach ($umkmData as $umk) {
            Umkm::firstOrCreate(['nama_usaha' => $umk['nama_usaha']], $umk);
        }

        // 4. Seed Aset & Inventaris RT
        $asetData = [
            [
                'nama' => 'Tenda Terop Besi Lipat 4x6 Meter',
                'deskripsi' => 'Tenda besi kokoh untuk acara pernikahan, khitanan, pengajian akbar, atau tasyakuran warga.',
                'kategori' => 'Tenda & Terop',
                'kondisi' => 'Baik',
                'lokasi' => 'Gudang Balai Warga RT 06',
                'jumlah' => 3,
                'status' => 'Aktif',
            ],
            [
                'nama' => 'Kursi Plastik Napolly Hijau Sandaran',
                'deskripsi' => 'Kursi plastik tebal berkualitas untuk acara pertemuan warga dan rapat RT.',
                'kategori' => 'Mebel & Tempat Duduk',
                'kondisi' => 'Baik',
                'lokasi' => 'Balai Warga RT 06',
                'jumlah' => 60,
                'status' => 'Aktif',
            ],
            [
                'nama' => 'Sound System Portable Wireless + 2 Mic',
                'deskripsi' => 'Speaker aktif portable dengan koneksi Bluetooth, USB, dan 2 mic wireless UHF untuk kegiatan senam dan rapat warga.',
                'kategori' => 'Elektronik & Audio',
                'kondisi' => 'Baik',
                'lokasi' => 'Rumah Pengurus RT 06',
                'jumlah' => 2,
                'status' => 'Aktif',
            ],
            [
                'nama' => 'Alat Fogging Disinfektan & Pembasmi Nyamuk',
                'deskripsi' => 'Mesin fogging thermal bensin untuk penyemprotan rutin pencegahan demam berdarah.',
                'kategori' => 'Kesehatan Lingkungan',
                'kondisi' => 'Baik',
                'lokasi' => 'Pos Ronda RW 19',
                'jumlah' => 2,
                'status' => 'Aktif',
            ],
        ];

        foreach ($asetData as $ast) {
            Aset::firstOrCreate(['nama' => $ast['nama']], $ast);
        }

        // 5. Seed Surat Pengajuan Contoh
        if ($warga1) {
            $suratData = [
                [
                    'id_user' => $warga1->id,
                    'nama' => $warga1->nama,
                    'jenis_surat' => 'Surat Pengantar SKCK',
                    'nik' => $warga1->nik ?? '3275010101900005',
                    'ttl' => 'Bekasi, 15 Mei 1995',
                    'alamat' => $warga1->alamat ?? 'Jl. Mawar No. 10 RT 06/RW 19',
                    'tujuan' => 'Melengkapi persyaratan lamaran pekerjaan di BUMN',
                    'nama_rt' => 'Dimas Purnomo',
                    'nama_rw' => 'Fabian',
                    'kelurahan' => 'Bekasi Jaya',
                    'kecamatan' => 'Bekasi Timur',
                    'kota' => 'Kota Bekasi',
                    'tanggal_permohonan' => Carbon::now()->subDays(1)->format('Y-m-d'),
                    'detail_usaha' => null,
                    'keterangan' => 'Pengantar ke Polsek Bekasi Timur',
                    'status' => 'Disetujui',
                    'created_at' => Carbon::now()->subDays(1),
                ],
                [
                    'id_user' => $warga2 ? $warga2->id : $warga1->id,
                    'nama' => $warga2 ? $warga2->nama : 'Tasya Rahmadani',
                    'jenis_surat' => 'Surat Keterangan Usaha (SKU)',
                    'nik' => ($warga2 && $warga2->nik) ? $warga2->nik : '3275010101900006',
                    'ttl' => 'Bekasi, 22 Agustus 1998',
                    'alamat' => ($warga2 && $warga2->alamat) ? $warga2->alamat : 'Jl. Anggrek No. 12 RT 06/RW 19',
                    'tujuan' => 'Pengajuan Bantuan Modal Usaha Mikro KUR Bank Mandiri',
                    'nama_rt' => 'Dimas Purnomo',
                    'nama_rw' => 'Fabian',
                    'kelurahan' => 'Bekasi Jaya',
                    'kecamatan' => 'Bekasi Timur',
                    'kota' => 'Kota Bekasi',
                    'tanggal_permohonan' => Carbon::now()->format('Y-m-d'),
                    'detail_usaha' => 'Usaha Makanan Ringan & Kue Kering Rumahan',
                    'keterangan' => 'Telah diverifikasi usaha aktif di rumah',
                    'status' => 'Menunggu',
                    'created_at' => Carbon::now(),
                ],
            ];

            foreach ($suratData as $srt) {
                SuratPengajuan::firstOrCreate(
                    ['id_user' => $srt['id_user'], 'jenis_surat' => $srt['jenis_surat']],
                    $srt
                );
            }
        }

        // 6. Seed Bank Sampah
        if ($warga1) {
            $bankSampahData = [
                [
                    'id_user' => $warga1->id,
                    'nama_warga' => $warga1->nama,
                    'jenis_sampah' => 'Botol Plastik Bersih (PET)',
                    'berat_kg' => 8.5,
                    'harga_per_kg' => 3000,
                    'total_harga' => 25500,
                    'status' => 'approved',
                    'tanggal_pengumpulan' => Carbon::now()->subDays(3),
                    'tanggal_diproses' => Carbon::now()->subDays(3),
                    'catatan' => 'Setoran botol air mineral bersih tanpa label',
                ],
                [
                    'id_user' => $warga2 ? $warga2->id : $warga1->id,
                    'nama_warga' => $warga2 ? $warga2->nama : 'Tasya Rahmadani',
                    'jenis_sampah' => 'Kardus Tebal / Box Paket',
                    'berat_kg' => 12.0,
                    'harga_per_kg' => 1500,
                    'total_harga' => 18000,
                    'status' => 'approved',
                    'tanggal_pengumpulan' => Carbon::now()->subDays(1),
                    'tanggal_diproses' => Carbon::now()->subDays(1),
                    'catatan' => 'Kardus terikat rapi',
                ],
            ];

            foreach ($bankSampahData as $bs) {
                BankSampah::firstOrCreate(
                    [
                        'id_user' => $bs['id_user'],
                        'jenis_sampah' => $bs['jenis_sampah'],
                        'tanggal_pengumpulan' => $bs['tanggal_pengumpulan'],
                    ],
                    $bs
                );
            }
        }
    }
}

