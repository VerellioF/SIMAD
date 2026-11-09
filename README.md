# Sistem Informasi Madrasah Terpadu (SIMAD)
### MI Plus Raudlatul Muta'allimin Kalipucung, Sanankulon, Blitar

SIMAD adalah platform sistem informasi manajemen madrasah berbasis web modern yang dirancang untuk mengintegrasikan seluruh operasional madrasah ibtidaiyah/dasar: portal publik, PPDB online, pembayaran SPP terintegrasi payment gateway (QRIS & VA), buku nilai akademik (raport & leger), presensi harian, kartu pelajar digital (KTS), mutaba'ah ibadah, dan notifikasi WhatsApp terotomatisasi.

---

## Fitur Utama

- **Portal Web Publik & PPDB Online:** Pendaftaran santri baru mandiri, unduh bukti pendaftaran PDF berproteksi DOB (UU PDP), dan pelacakan status seleksi.
- **Payment Gateway Terpadu (QRIS & Virtual Account):** Checkout pembayaran SPP dan pendaftaran PPDB dengan verifikasi otomatis dan simulasi sandbox.
- **Notifikasi WhatsApp Otomatis:** Pengiriman kwitansi SPP, notifikasi ketidakhadiran siswa, dan pengumuman madrasah via gateway API.
- **Portal Guru & Wali Kelas:** Presensi siswa berbasis jadwal, input nilai mode spreadsheet pintar, cetak rapor PDF, dan leger nilai (HTML/CSV).
- **Portal Wali Murid:** Dashboard pemantauan kehadiran anak, jurnal mutaba'ah ibadah harian, surat izin sakit/izin, riwayat tahfidz, dan pembayaran SPP online.
- **Portal Tata Usaha (TU) & Admin:** Manajemen master data guru/siswa, cetak kartu pelajar (KTS), cetak kartu ujian asesmen, broadcast tagihan SPP, dan export data format EMIS Kemenag.

---

## Lisensi & Ketentuan Penggunaan (License)

Proyek ini dilindungi oleh hak cipta dan didistribusikan di bawah skema **Dual-Licensing (Lisensi Ganda)**:

### 1. Penggunaan Non-Komersial (Gratis)
Diizinkan secara gratis untuk:
- Pembelajaran, riset akademik, dan eksplorasi kode sumber personal.
- Implementasi internal mandiri oleh sekolah/madrasah nirlaba tanpa pihak ketiga berbayar.

### 2. Penggunaan Komersial (Berbayar)
Setiap entitas bisnis, agensi perangkat lunak, penyedia jasa IT, atau individu yang:
- Menjual, mendistribusikan ulang (*reselling*), atau mengemas sistem ini sebagai produk berbayar.
- Menyediakan layanan *SaaS* atau *Managed Hosting* berbayar kepada sekolah/klien pihak ketiga.
- Melakukan *white-labeling* atau rebranding komersial.

**WAJIB membeli Lisensi Komersial resmi.** Rincian lengkap ketentuan lisensi dapat dibaca pada file [LICENSE.md](LICENSE.md).

---

## Pembelian Lisensi Komersial & Kerjasama

Untuk konsultasi implementasi resmi, kustomisasi fitur, atau pembelian Lisensi Komersial:
- **Email:** `license@miplus-kalipucung.sch.id`
- **WhatsApp Kemitraan:** +62 812-3456-7890
- **Pengembang:** Tim SIMAD & Yayasan Raudlatul Muta'allimin

---

## Persyaratan Sistem

- PHP `>= 8.3` (Disarankan PHP `>= 8.4.1`)
- Composer `>= 2.7`
- Database: SQLite / MySQL `>= 8.0` / MariaDB `>= 10.5`
- Ekstensi PHP: `pdo`, `mbstring`, `intl`, `gd`, `zip`, `xml`

---

## Panduan Instalasi Lokal

```bash
# 1. Clone repository
git clone https://github.com/username/repository-name.git
cd repository-name

# 2. Install dependensi
composer install
npm install && npm run build

# 3. Salin environment & generate app key
cp .env.example .env
php artisan key:generate

# 4. Migrasi & seed basis data
php artisan migrate --seed

# 5. Jalankan server lokal
php artisan serve
```

---

<p align="center">
  Developed by <a href="https://verellio.com/id"><strong>Verellio</strong></a>
</p>

