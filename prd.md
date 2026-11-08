# Product Requirements Document (PRD)
## Sistem Informasi Madrasah Terpadu (SIMAD)
### MI Plus Raudlatul Muta'allimin Kalipucung, Blitar

---

## 1. Executive Summary & Visi Produk
Sistem Informasi Madrasah Terpadu (SIMAD) MI Plus Raudlatul Muta'allimin Kalipucung adalah platform web terpadu yang memadukan landing page publik, portal PPDB online, serta portal operasional madrasah (SIAKAD) multi-peran (Admin/TU, Guru, dan Wali Murid). 

Tujuan utama sistem ini adalah mendigitalkan administrasi harian, memangkas proses manual berbasis kertas, meningkatkan transparansi akademik dan keagamaan (Tahfidz/Ibadah) kepada wali murid, serta memperluas jangkauan promosi madrasah.

---

## 2. Target Pengguna & Persona

| Aktor | Persona Singkat | Kebutuhan Utama |
| :--- | :--- | :--- |
| **Calon Wali Murid / Publik** | Orang tua yang mencari madrasah berkualitas di Blitar. | Mengakses profil, fasilitas, prestasi, dan mendaftar PPDB secara online dari rumah. |
| **Wali Murid Santri** | Orang tua murid aktif dengan mobilitas tinggi. | Memantau kehadiran anak, setoran hafalan Qur'an, nilai tugas, izin sakit, dan membayar SPP/infaq. |
| **Guru & Wali Kelas** | Tenaga pengajar kelas & ustadz pembina tahfidz. | Presensi cepat, input nilai harian/PTS/PAS, pencatatan mutaba'ah tahfidz, dan validasi izin sakit. |
| **Staf TU & Admin Madrasah** | Operator sekolah dan bendahara madrasah. | Manajemen master data siswa/guru/rombel, seleksi berkas PPDB, verifikasi SPP, dan cetak dokumen resmi. |

---

## 3. Cakupan Fitur & Kebutuhan Fungsional

### 3.1. Modul Publik & Promosi
* **Landing Page Profil:**
  * Profil sejarah madrasah, visi, misi, tenaga pendidik, dan fasilitas.
  * Galeri multimedia kegiatan (seni pentas, hadrah/rebana, tari, pramuka, drumband).
  * Widget Islami terintegrasi: jadwal sholat fardhu khusus wilayah Blitar dan sekitarnya.
* **PPDB Online (Penerimaan Peserta Didik Baru):**
  * Formulir multi-step pendaftaran calon santri baru (biodata santri, orang tua, riwayat sekolah).
  * Upload dokumen pendukung: Akta Kelahiran, Kartu Keluarga, dan KTP Orang Tua (format JPG/PNG/PDF).
  * Cetak lembar bukti pendaftaran mandiri berformat PDF dengan nomor registrasi unik.
  * Halaman cek status seleksi PPDB mandiri menggunakan NIK / Nomor Pendaftaran.

### 3.2. Modul Wali Murid (Parent Portal)
* **Presensi Anak:** Cek rekap kehadiran harian dan kehadiran per jam mata pelajaran.
* **Pengajuan Surat Izin / Sakit:** Form pengajuan ketidakhadiran disertai lampiran foto surat dokter/surat orang tua.
* **Monitoring Tahfidz & Ibadah:**
  * Riwayat hafalan Al-Qur'an (Surat, Ayat, Predikat Kelancaran/Tajwid, dan Catatan Ustadz).
  * Buku Mutaba'ah Yaumiyah (monitoring sholat 5 waktu & dhuha).
* **Akademik:** Cek nilai formatif harian, nilai sumatif (PTS/PAS), dan catatan wali kelas.
* **Keuangan (SPP & Infaq):**
  * Daftar tagihan aktif dan riwayat pembayaran lampau.
  * Informasi rekening resmi BSI/Bank madrasah beserta panduan transfer.
  * Unggah bukti transfer dan unduh kwitansi pembayaran digital setelah diverifikasi.

### 3.3. Modul Guru & Tenaga Pengajar (Teacher Portal)
* **Jurnal Presensi 1-Klik:** Absensi kehadiran siswa per jam mata pelajaran atau per kelas harian.
* **Validasi Izin Siswa:** Menerima notifikasi dan menyetujui/menolak pengajuan izin sakit dari orang tua.
* **Buku Penilaian Akademik:**
  * Input nilai harian (tugas, PR, ulangan) dan ujian semester per mata pelajaran.
  * Opsi ekspor/impor rekapan nilai via spreadsheet (.xlsx).
* **Jurnal Hafalan Qur'an (Tahfidz):** Formulir pencatatan setoran hafalan santri (nama surat, ayat, kelancaran, catatan evaluasi).

### 3.4. Modul Administrator & Tata Usaha (Backoffice)
* **Master Data:**
  * Kelola data siswa aktif, data guru/staf, tahun ajaran, dan rombongan belajar (rombel).
  * Fitur kenaikan kelas dan kelulusan santri.
* **Manajemen PPDB:** Verifikasi berkas, penetapan status (Diterima / Cadangan / Ditolak), dan ekspor data pendaftar format EMIS Kemenag.
* **Tata Kelola SPP & Kas:**
  * Verifikasi konfirmasi pembayaran dari wali murid.
  * Rekapitulasi tunggakan santri per kelas dan laporan arus kas masuk madrasah.
* **Mini CMS & Pengumuman:** Publikasi berita kegiatan, agenda kalender akademik, dan pembaruan galeri.

---

## 4. Kebutuhan Non-Fungsional (NFR)
* **Kinerja:** Waktu pemuatan halaman publik < 2 detik pada koneksi seluler 4G standar.
* **Responsif:** Antarmuka optimal di perangkat mobile (Android/iOS) dan desktop browser.
* **Keamanan:** Proteksi CSRF, sanitasi input berkas unggahan, hashing password (bcrypt/Argon2), dan pemisahan hak akses berbasis peran (RBAC).
* **Ketersediaan & Keandalan:** Backup database harian dan penanganan file storage yang terisolasi aman.

---

## 5. Rencana Peluncuran Bertahap (Phased Rollout)
* **Fase 1 (Publik & PPDB):** Peluncuran landing page madrasah dan formulir PPDB online untuk mendukung penjaringan santri baru.
* **Fase 2 (Presensi & Izin):** Implementasi modul presensi guru dan cek absen/surat izin untuk wali murid.
* **Fase 3 (Akademik & SPP):** Pembukaan modul input nilai, jurnal setoran tahfidz, dan verifikasi keuangan SPP.