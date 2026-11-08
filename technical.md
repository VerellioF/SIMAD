# Technical Specification & Architecture Document
## Sistem Informasi Madrasah Terpadu (SIMAD)
### MI Plus Raudlatul Muta'allimin Kalipucung, Blitar

---

## 1. Technology Stack Selection

* **Core Framework:** Laravel 11.x (PHP 8.2+)
* **Admin & Portal Engine:** Filament PHP v3 (TALL Stack: Tailwind CSS, Alpine.js, Laravel, Livewire)
* **Frontend Landing Page:** Laravel Blade Views + Tailwind CSS + Vanilla JS (ringan, ramah SEO)
* **Database:** MySQL 8.0 / MariaDB 10.6+
* **Document & PDF Generation:** `barryvdh/laravel-dompdf` (Kwitansi SPP, Form PPDB, Rekap Nilai)
* **Excel Import / Export:** `maatwebsite/excel` (Ekspor pendaftar format EMIS, rekap nilai)
* **Icons & UI:** Heroicons & FontAwesome 6

---

## 2. Arsitektur Multi-Portal & Autentikasi

Sistem menggunakan panel multi-tenant berbasis peran (*Multi-Panel Filament v3*) dalam satu basis kode:

```text
app/
├── Filament/
│   ├── Admin/         --> Prefix URL: /admin (Panel Staf TU, Kepala Madrasah, Operator)
│   ├── Teacher/       --> Prefix URL: /guru  (Panel Wali Kelas & Guru Mata Pelajaran)
│   └── Parent/        --> Prefix URL: /wali  (Panel Khusus Orang Tua / Wali Murid)
└── Http/Controllers/  --> Prefix URL: /      (Web Publik, Berita, & Form PPDB Online)