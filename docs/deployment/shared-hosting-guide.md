# Panduan Deployment SIMAD di Shared Hosting (cPanel / Non-Node.js)

Dokumen ini memandu proses rilis Sistem Informasi Madrasah Terpadu (SIMAD) MI Plus Raudlatul Muta'allimin Kalipucung ke server *shared hosting* cPanel yang **tidak memiliki runtime Node.js**.

---

## 1. Prinsip Zero-Node.js pada Server

1. **Aset CSS/JS Dibangun Lokal:** Server shared hosting hanya menyajikan file statis dari `public/build/` dan file inti Filament yang sudah dipublikasikan.
2. **Tidak Perlu SSH / Terminal di Hosting:** Seluruh kode, migrasi, dan dependensi vendor dapat diunggah langsung via File Manager cPanel.

---

## 2. Langkah-Langkah Deployment

### Langkah 1: Build Arsip Produksi di Komputer Lokal
Jalankan skrip build otomatis dari terminal PowerShell di folder proyek ini:
```powershell
.\bin\build-for-production.ps1
```
Skrip ini akan:
- Menjalankan 100% test otomatis (PHPUnit).
- Mengompilasi CSS/JS Tailwind via `npm run build`.
- Menghasilkan file arsip siap unggah: `simad-production-YYYYMMDD_HHMMSS.zip`.

---

### Langkah 2: Setup Database MySQL di cPanel
1. Buka menu **MySQL Databases** di cPanel hosting Anda.
2. Buat database baru, contoh: `username_simad`.
3. Buat user database baru dan berikan hak akses penuh (**All Privileges**) ke database tersebut.
4. Buka **phpMyAdmin**, pilih database tersebut, dan klik tab **Import**. Impor skrip SQL DDL dari [skema_basis_data_simad.md](file:///d:/MIRM/skema_basis_data_simad.md) atau jalankan migrasi.

---

### Langkah 3: Ekstrak File di File Manager cPanel
Struktur direktori yang direkomendasikan pada shared hosting:
```text
/home/username/
├── simad_app/               <-- Tempatkan seluruh file aplikasi di sini (di luar public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── vendor/
│   └── .env
└── public_html/             <-- Tempatkan isi dari folder 'public/' di sini
    ├── build/
    ├── css/
    ├── js/
    ├── index.php
    └── .htaccess
```

> **Tips:** Sesuaikan baris path di `public_html/index.php`:
> ```php
> require __DIR__.'/../simad_app/vendor/autoload.php';
> $app = require_once __DIR__.'/../simad_app/bootstrap/app.php';
> ```

---

### Langkah 4: Konfigurasi File `.env` Produksi
Buat file `.env` di dalam folder `simad_app/` dan isi dengan konfigurasi hosting:
```env
APP_NAME="SIMAD MI Plus Raudlatul Muta'allimin"
APP_ENV=production
APP_KEY=base64:... (gunakan app key proyek Anda)
APP_DEBUG=false
APP_URL=https://namadomain-madrasah.sch.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_simad
DB_USERNAME=username_dbuser
DB_PASSWORD=password_rahasia_db

FILESYSTEM_DISK=public
```

---

### Langkah 5: Storage Symlink Tanpa Terminal (Khusus Shared Hosting)
Jika shared hosting tidak menyediakan akses SSH untuk menjalankan `php artisan storage:link`, buat file sementara bernama `link.php` di dalam `public_html/`:
```php
<?php
symlink('/home/username/simad_app/storage/app/public', __DIR__.'/storage');
echo "Symlink storage berhasil dibuat!";
```
Akses `https://namadomain-madrasah.sch.id/link.php` sekali di browser, lalu segera hapus file `link.php` demi keamanan.

---

## 3. Akun Login Default Awal
Setelah database siap, akun awal dapat dibuat melalui seeder atau langsung di tabel `users` (dengan password di-hash `bcrypt`):
- **Admin / TU:** Role `'admin'` → URL: `https://domain/admin`
- **Guru / Wali Kelas:** Role `'teacher'` → URL: `https://domain/guru`
- **Wali Murid:** Role `'parent'` → URL: `https://domain/wali`
- **Website Publik & PPDB Online:** `https://domain/`
