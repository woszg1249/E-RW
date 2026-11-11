<div class="row g-4">
    <!-- Menu Navigasi Topik Panduan -->
    <div class="col-md-3">
        <div class="card-custom p-3 sticky-top" style="top: 80px;">
            <div class="fw-bold text-dark small mb-2 text-uppercase tracking-wide">Daftar Isi Panduan</div>
            <div class="list-group list-group-flush small" id="listPanduan">
                <a href="#p-warga" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold active">
                    <i class="fa-solid fa-users me-2"></i> 1. Panduan Warga
                </a>
                <a href="#p-kk" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-address-card me-2"></i> 2. Kartu Keluarga (KK)
                </a>
                <a href="#p-surat" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-envelope me-2"></i> 3. Surat Pengantar RT
                </a>
                <a href="#p-iuran" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-receipt me-2"></i> 4. Tagihan & Bayar Iuran
                </a>
                <a href="#p-bankwarga" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-building-columns me-2"></i> 5. Bank Warga & IoT
                </a>
                <a href="#p-banksampah" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-recycle me-2"></i> 6. Bank Sampah Terpadu
                </a>
                <a href="#p-posyandu" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-baby me-2"></i> 7. Posyandu Balita KIA
                </a>
                <a href="#p-admin" class="list-group-item list-group-item-action border-0 rounded-3 py-2 fw-semibold">
                    <i class="fa-solid fa-user-shield me-2"></i> 8. Panduan Pengurus RT
                </a>
            </div>
        </div>
    </div>

    <!-- Isi Penjelasan Tiap Topik -->
    <div class="col-md-9">
        <!-- 1. Panduan Warga -->
        <div class="card-custom p-4 mb-4" id="p-warga">
            <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-users me-2"></i>1. Panduan Akun Warga (Login & Pendaftaran)</h5>
            <p class="small text-muted mb-3">Setiap warga di wilayah RT 06 dan RT 07 RW 19 dapat mendaftar mandiri menggunakan NIK dan No KK yang terdaftar.</p>
            <div class="row g-3 small">
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <strong class="d-block mb-1 text-dark">Langkah Pendaftaran Akun Warga Baru:</strong>
                        <ol class="mb-0 ps-3 text-secondary">
                            <li>Buka halaman <a href="{{ route('register') }}" class="fw-bold text-success">Daftar Warga</a>.</li>
                            <li>Masukkan <strong>NIK (16 Digit)</strong> dan <strong>Nomor KK</strong> Anda. Sistem akan memeriksa kecocokan data dengan database kependudukan RW 19.</li>
                            <li>Isi username, alamat email aktif, dan buat kata sandi (password).</li>
                            <li>Klik tombol <strong>Daftar Akun</strong>. Akun Anda langsung aktif dan dapat digunakan untuk login.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Kartu Keluarga -->
        <div class="card-custom p-4 mb-4" id="p-kk">
            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-address-card me-2"></i>2. Melihat & Cetak Kartu Keluarga (KK)</h5>
            <p class="small text-muted mb-2">Sistem menjamin privasi kependudukan di mana akun warga hanya dapat melihat anggota keluarganya sendiri.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ul class="mb-0 ps-3 text-secondary">
                    <li>Urutan anggota keluarga otomatis disesuaikan dengan aturan Dukcapil: <strong>Kepala Keluarga selalu di urutan nomor 1 teratas</strong>, disusul Istri dan Anak.</li>
                    <li>Warga dapat mencetak format resmi Kartu Keluarga dengan mengklik tombol <strong>Cetak KK</strong>. Dokumen ini dilengkapi tanda tangan resmi Ketua RT dan Kepala Dinas Kependudukan.</li>
                </ul>
            </div>
        </div>

        <!-- 3. Surat Pengantar RT -->
        <div class="card-custom p-4 mb-4" id="p-surat">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-envelope-open-text me-2"></i>3. Mengajukan Surat Pengantar RT Online</h5>
            <p class="small text-muted mb-2">Tidak perlu lagi datang ke rumah RT hanya untuk meminta kertas pengantar.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ol class="mb-0 ps-3 text-secondary">
                    <li>Pilih menu <strong>Surat Pengantar RT</strong> di sidebar navigasi.</li>
                    <li>Klik <strong>Ajukan Surat Baru</strong>, lalu pilih keperluan (e.g. Surat Keterangan Domisili, Pembuatan KTP/KK, SKCK, Usaha, dll.).</li>
                    <li>Isi data tujuan dan keterangan tambahan.</li>
                    <li>Setelah diajukan, Ketua RT (Dimas untuk RT 06 / Bambang untuk RT 07) akan memverifikasi permohonan Anda.</li>
                    <li>Setelah disetujui, tombol <strong>Cetak Surat Pengantar</strong> akan aktif dan dapat langsung Anda unduh/print dengan stempel dan tanda tangan resmi.</li>
                </ol>
            </div>
        </div>

        <!-- 4. Tagihan & Iuran Warga -->
        <div class="card-custom p-4 mb-4" id="p-iuran">
            <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-receipt me-2"></i>4. Pembayaran Tagihan Iuran Lingkungan</h5>
            <p class="small text-muted mb-2">Setiap iuran kas lingkungan RT diterbitkan secara transparan.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ol class="mb-0 ps-3 text-secondary">
                    <li>Masuk ke menu <strong>Tagihan Iuran Warga</strong>.</li>
                    <li>Pilih tagihan yang berstatus <span class="badge bg-danger bg-opacity-10 text-danger">Belum Lunas</span>, lalu klik tombol <strong>Bayar</strong>.</li>
                    <li>Pilih metode pembayaran:
                        <ul>
                            <li><strong>Bank Digital Warga:</strong> Saldo kas digital warga Anda langsung terpotong secara instan tanpa perlu upload bukti transfer.</li>
                            <li><strong>QRIS Digital:</strong> Pindai QR Code menggunakan aplikasi GoPay, OVO, ShopeePay, DANA, atau Mobile Banking BCA/Mandiri.</li>
                            <li><strong>Transfer Bank & Tunai:</strong> Lakukan pembayaran langsung ke pengurus RT setempat.</li>
                        </ul>
                    </li>
                    <li>Setelah pembayaran berhasil, status berubah menjadi <span class="badge bg-success bg-opacity-10 text-success">Lunas</span> dan transaksi otomatis masuk ke pembukuan Kas RT.</li>
                </ol>
            </div>
        </div>

        <!-- 5. Bank Warga & IoT -->
        <div class="card-custom p-4 mb-4" id="p-bankwarga">
            <h5 class="fw-bold text-info mb-3"><i class="fa-solid fa-building-columns me-2"></i>5. Fitur Bank Digital Warga & IoT e-KTP</h5>
            <p class="small text-muted mb-2">Inovasi dompet digital kas warga untuk mempermudah transaksi lingkungan RT RW.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ul class="mb-0 ps-3 text-secondary">
                    <li><strong>Buka Rekening Otomatis:</strong> Nomor rekening langsung terbit terhubung dengan akun warga Anda (e.g. <code>BW-1906-880012</code>).</li>
                    <li><strong>Top-Up Saldo:</strong> Klik Top-Up, pilih nominal instan (Rp 25.000, Rp 50.000, Rp 100.000) dan pilih metode transfer Virtual Account atau QRIS E-Wallet.</li>
                    <li><strong>Otomatis Terima Hasil Sampah:</strong> Setiap menyetor sampah di Bank Sampah, nilai uangnya langsung masuk ke saldo rekening Anda!</li>
                    <li><strong>Alat IoT Pos RT:</strong> Tempelkan kartu e-KTP ke sensor pembaca IoT RFID di pos RT untuk mengecek saldo atau memvalidasi pembayaran secara kilat.</li>
                </ul>
            </div>
        </div>

        <!-- 6. Bank Sampah -->
        <div class="card-custom p-4 mb-4" id="p-banksampah">
            <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-recycle me-2"></i>6. Pengelolaan Bank Sampah Terpadu</h5>
            <p class="small text-muted mb-2">Ubah sampah rumah tangga menjadi saldo tabungan bernilai rupiah.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ol class="mb-0 ps-3 text-secondary">
                    <li>Kumpulkan dan pilah sampah anorganik di rumah (botol plastik bersih, kardus bekas, kaleng, dll.).</li>
                    <li>Bawa ke Pos Bank Sampah RW 19 pada jadwal penimbangan atau laporkan via menu <strong>Bank Sampah</strong>.</li>
                    <li>Petugas menimbang berat sampah dan mencatat ke sistem.</li>
                    <li>Total rupiah hasil sampah secara <strong>otomatis langsung ditransfer ke rekening Bank Warga Anda</strong> tanpa perlu menunggu uang tunai kembalian!</li>
                </ol>
            </div>
        </div>

        <!-- 7. Posyandu Balita -->
        <div class="card-custom p-4 mb-4" id="p-posyandu">
            <h5 class="fw-bold text-danger mb-3"><i class="fa-solid fa-baby me-2"></i>7. Posyandu Balita & Cetak Hasil Kesehatan KIA</h5>
            <p class="small text-muted mb-2">Pemantauan tumbuh kembang balita sesuai standar kurva WHO dan Kementerian Kesehatan RI.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ul class="mb-0 ps-3 text-secondary">
                    <li>Bawa balita ke Posyandu Melati RW 19 untuk penimbangan rutin setiap bulan.</li>
                    <li>Kader mencatat berat badan (BB), panjang badan (TB), dan lingkar kepala (LK).</li>
                    <li>Sistem otomatis mengevaluasi status gizi anak (Gizi Baik/Normal, Kurang, atau Risiko Stunting).</li>
                    <li>Orang tua dapat melihat riwayat pemeriksaan dan mengklik tombol <strong>Cetak Hasil</strong> untuk mendapatkan surat keterangan hasil pemeriksaan resmi yang ditandatangani Kader Posyandu dan Ketua RT.</li>
                </ul>
            </div>
        </div>

        <!-- 8. Panduan Pengurus RT / Admin -->
        <div class="card-custom p-4 mb-4" id="p-admin">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-shield me-2"></i>8. Panduan Khusus Pengurus RT & Admin RW</h5>
            <p class="small text-muted mb-2">Fitur-fitur eksklusif pengelolaan wilayah untuk Ketua RT 06, Ketua RT 07, dan RW 19.</p>
            <div class="p-3 bg-light rounded-3 border small mb-0">
                <ul class="mb-0 ps-3 text-secondary">
                    <li><strong>Buku Kas RT:</strong> Catat setiap pengeluaran operasional (keamanan, kebersihan, perbaikan jalan) dan pemasukan iuran. Unggah bukti nota kuitansi kas.</li>
                    <li><strong>Laporan Keuangan Bulanan:</strong> Filter mutasi kas berdasarkan bulan dan tahun, lalu cetak laporan rekapitulasi bertanda tangan Ketua RT/RW.</li>
                    <li><strong>Buat Tagihan Iuran:</strong> Buat tagihan iuran bulanan untuk seluruh warga dengan sekali klik.</li>
                    <li><strong>Persetujuan Surat Pengantar:</strong> Periksa berkas permohonan warga di menu Surat Pengantar dan berikan persetujuan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
