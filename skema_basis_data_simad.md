# Skema Database: Sistem Informasi Madrasah Terpadu (SIMAD)
### MI Plus Raudlatul Muta'allimin Kalipucung, Sanankulon, Blitar

Dokumen ini memuat skema relasi basis data (RDBMS MySQL 8.0+ / MariaDB 10.6+) lengkap beserta kamus data dan sintaks migrasi SQL untuk seluruh modul: Autentikasi & Pengguna, Master Data Akademik, PPDB Online, Presensi & Izin, Penilaian & Tahfidz, Keuangan (SPP/Infaq), serta CMS Publik.

---

## 1. Diagram Relasi Entitas (ERD Textual)

```text
[users] 
  ├── (1:N) ──> [students] (sebagai wali murid / parent_id)
  ├── (1:N) ──> [classrooms] (sebagai wali kelas / homeroom_teacher_id)
  ├── (1:N) ──> [attendances] (sebagai guru pencatat / recorded_by)
  ├── (1:N) ──> [leave_requests] (diajukan parent_id, diverifikasi reviewed_by)
  ├── (1:N) ──> [tahfidz_records] (pengampu / teacher_id)
  ├── (1:N) ──> [academic_grades] (guru pengampu / teacher_id)
  └── (1:N) ──> [tuition_payments] (diverifikasi / verified_by)

[academic_years]
  ├── (1:N) ──> [classrooms]
  ├── (1:N) ──> [academic_grades]
  └── (1:N) ──> [tuition_bills]

[classrooms]
  ├── (1:N) ──> [students]
  ├── (1:N) ──> [attendances]
  └── (1:N) ──> [academic_grades]

[students]
  ├── (1:N) ──> [attendances]
  ├── (1:N) ──> [leave_requests]
  ├── (1:N) ──> [tahfidz_records]
  ├── (1:N) ──> [academic_grades]
  └── (1:N) ──> [tuition_bills]

[tuition_bills]
  └── (1:N) ──> [tuition_payments]

[ppdb_registrations] (Mandiri / Calon Santri)
[articles], [school_galleries], [announcements] (Konten Publik)
```

---

## 2. Kamus Data & Spesifikasi Entitas

### 2.1. Modul Autentikasi & Akun (`users`)
Menyimpan akun login terpusat untuk multi-guard portal (Admin/TU, Guru/Wali Kelas, dan Wali Murid).
* `id` (`BIGINT`, PK, Auto Increment)
* `name` (`VARCHAR(255)`): Nama lengkap pengguna beserta gelar jika ada.
* `email` (`VARCHAR(255)`, Unique, Nullable): Alamat email (opsional untuk akun wali murid).
* `phone_number` (`VARCHAR(30)`, Unique): Nomor WhatsApp aktif untuk autentikasi dan notifikasi gateway.
* `password` (`VARCHAR(255)`): Hash password (bcrypt / Argon2).
* `role` (`ENUM('admin', 'teacher', 'parent')`): Penentu batasan hak akses portal Filament/Laravel.
* `avatar_url` (`VARCHAR(255)`, Nullable): Path file pasfoto profil pengguna.
* `is_active` (`BOOLEAN`, Default: `TRUE`): Status keaktifan akun.
* `remember_token` (`VARCHAR(100)`, Nullable)
* `created_at`, `updated_at` (`TIMESTAMP`)

---

### 2.2. Modul Master Data Akademik (`academic_years`, `classrooms`, `subjects`, `students`)

#### `academic_years`
Tahun pelajaran dan semester yang berjalan.
* `id` (`BIGINT`, PK, Auto Increment)
* `name` (`VARCHAR(50)`): Format penamaan, contoh `'2026/2027'`.
* `semester` (`ENUM('ganjil', 'genap')`): Semester aktif.
* `is_active` (`BOOLEAN`, Default: `FALSE`): Penanda tahun ajaran sistem saat ini.
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `classrooms`
Data rombongan belajar (rombel) per tahun ajaran.
* `id` (`BIGINT`, PK, Auto Increment)
* `academic_year_id` (`BIGINT`, FK -> `academic_years.id`, ON DELETE CASCADE)
* `homeroom_teacher_id` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable): Wali kelas pengampu.
* `name` (`VARCHAR(50)`): Nama rombel (contoh: `'Kelas 1-A'`, `'Kelas 3-B'`, `'Kelas 4-Plus'`).
* `grade_level` (`TINYINT`): Tingkatan kelas (1 sampai 6).
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `subjects`
Katalog mata pelajaran kurikulum madrasah.
* `id` (`BIGINT`, PK, Auto Increment)
* `code` (`VARCHAR(20)`, Unique): Kode unik mapel (cth: `'QH-MI'`, `'FIQ-01'`, `'MTK-01'`).
* `name` (`VARCHAR(100)`): Nama mata pelajaran.
* `category` (`ENUM('agama', 'umum', 'muatan_lokal')`): Kelompok bidang studi.
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `students`
Data profil santri aktif madrasah.
* `id` (`BIGINT`, PK, Auto Increment)
* `parent_id` (`BIGINT`, FK -> `users.id`, ON DELETE RESTRICT): Relasi ke akun wali murid.
* `classroom_id` (`BIGINT`, FK -> `classrooms.id`, ON DELETE SET NULL, Nullable): Rombel santri saat ini.
* `nis` (`VARCHAR(30)`, Unique, Nullable): Nomor Induk Siswa lokal.
* `nisn` (`VARCHAR(30)`, Unique, Nullable): Nomor Induk Siswa Nasional resmi Kemenag/Kemdikbud.
* `full_name` (`VARCHAR(255)`): Nama lengkap santri sesuai Akta Kelahiran.
* `nickname` (`VARCHAR(100)`, Nullable): Nama panggilan santri.
* `gender` (`ENUM('L', 'P')`): Jenis kelamin.
* `birth_place` (`VARCHAR(100)`): Tempat kelahiran.
* `birth_date` (`DATE`): Tanggal kelahiran.
* `address` (`TEXT`): Alamat domisili lengkap.
* `status` (`ENUM('active', 'graduated', 'transferred', 'inactive')`, Default: `'active'`)
* `created_at`, `updated_at` (`TIMESTAMP`)

---

### 2.3. Modul PPDB Online (`ppdb_registrations`)
Menampung pendaftaran mandiri calon santri baru dari formulir website publik.
* `id` (`BIGINT`, PK, Auto Increment)
* `registration_number` (`VARCHAR(50)`, Unique): Format unik (cth: `PPDB-2026-0001`).
* `child_name` (`VARCHAR(255)`): Nama lengkap calon santri.
* `nik` (`VARCHAR(20)`): Nomor Induk Kependudukan anak.
* `gender` (`ENUM('L', 'P')`)
* `birth_place` (`VARCHAR(100)`)
* `birth_date` (`DATE`)
* `previous_school` (`VARCHAR(150)`): Asal lembaga TK/RA/PAUD.
* `parent_name` (`VARCHAR(255)`): Nama orang tua/wali pendaftar.
* `parent_whatsapp` (`VARCHAR(30)`): Nomor WhatsApp aktif untuk konfirmasi.
* `parent_job` (`VARCHAR(100)`, Nullable): Profesi orang tua.
* `address` (`TEXT`): Alamat tempat tinggal.
* `file_birth_certificate` (`VARCHAR(255)`): Path dokumen Akta Kelahiran.
* `file_family_card` (`VARCHAR(255)`): Path dokumen Kartu Keluarga.
* `file_photo` (`VARCHAR(255)`, Nullable): Path pasfoto calon santri.
* `status` (`ENUM('pending', 'verified', 'accepted', 'rejected')`, Default: `'pending'`)
* `admin_notes` (`TEXT`, Nullable): Catatan panitia seleksi.
* `created_at`, `updated_at` (`TIMESTAMP`)

---

### 2.4. Modul Presensi & Surat Izin (`attendances`, `leave_requests`)

#### `attendances`
Log pencatatan kehadiran harian santri.
* `id` (`BIGINT`, PK, Auto Increment)
* `student_id` (`BIGINT`, FK -> `students.id`, ON DELETE CASCADE)
* `classroom_id` (`BIGINT`, FK -> `classrooms.id`, ON DELETE CASCADE)
* `recorded_by` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable): Guru pencatat.
* `date` (`DATE`): Tanggal presensi.
* `status` (`ENUM('hadir', 'sakit', 'izin', 'alpa')`, Default: `'hadir'`)
* `notes` (`VARCHAR(255)`, Nullable): Keterangan tambahan / keterlambatan.
* `created_at`, `updated_at` (`TIMESTAMP`)
* *Unique Constraint:* `(student_id, date)` mencegah duplikasi data presensi pada tanggal yang sama.

#### `leave_requests`
Pengajuan surat izin / sakit dari wali murid melalui portal.
* `id` (`BIGINT`, PK, Auto Increment)
* `student_id` (`BIGINT`, FK -> `students.id`, ON DELETE CASCADE)
* `parent_id` (`BIGINT`, FK -> `users.id`, ON DELETE CASCADE): Akun orang tua pengaju.
* `reviewed_by` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable): Wali kelas peninjau.
* `start_date` (`DATE`): Tanggal awal izin.
* `end_date` (`DATE`): Tanggal akhir izin.
* `reason_type` (`ENUM('sakit', 'izin')`): Alasan ketidakhadiran.
* `description` (`TEXT`): Uraian alasan tidak masuk sekolah.
* `attachment_path` (`VARCHAR(255)`, Nullable): Foto surat dokter atau surat izin bertanda tangan.
* `status` (`ENUM('pending', 'approved', 'rejected')`, Default: `'pending'`)
* `review_notes` (`VARCHAR(255)`, Nullable): Catatan balasan wali kelas.
* `created_at`, `updated_at` (`TIMESTAMP`)

---

### 2.5. Modul Tahfidz & Penilaian Akademik (`tahfidz_records`, `academic_grades`)

#### `tahfidz_records`
Catatan progres hafalan Qur'an (Juz 30 & pembiasaan tartil).
* `id` (`BIGINT`, PK, Auto Increment)
* `student_id` (`BIGINT`, FK -> `students.id`, ON DELETE CASCADE)
* `teacher_id` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable): Guru pembimbing tahfidz.
* `date` (`DATE`): Tanggal setoran.
* `surah_name` (`VARCHAR(100)`): Nama surat yang dihafal.
* `verse_range` (`VARCHAR(50)`): Rentang ayat (cth: `'Ayat 1-20'`).
* `fluency_grade` (`ENUM('lancar', 'kurang_lancar', 'mengulang')`): Nilai kelancaran.
* `tajweed_grade` (`ENUM('mumtaz', 'jayyid', 'maqbul')`): Penguasaan hukum tajwid dan makhraj.
* `audio_recording_path` (`VARCHAR(255)`, Nullable): Rekaman suara jika setoran mandiri dari rumah.
* `notes` (`TEXT`, Nullable): Evaluasi dan nasehat guru.
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `academic_grades`
Rekapitulasi nilai harian dan ujian per semester.
* `id` (`BIGINT`, PK, Auto Increment)
* `student_id` (`BIGINT`, FK -> `students.id`, ON DELETE CASCADE)
* `classroom_id` (`BIGINT`, FK -> `classrooms.id`, ON DELETE CASCADE)
* `subject_id` (`BIGINT`, FK -> `subjects.id`, ON DELETE CASCADE)
* `academic_year_id` (`BIGINT`, FK -> `academic_years.id`, ON DELETE CASCADE)
* `teacher_id` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable)
* `grade_type` (`ENUM('harian', 'tugas', 'pts', 'pas')`): Jenis asesmen.
* `score` (`DECIMAL(5, 2)`): Nilai numerik (0.00 - 100.00).
* `notes` (`VARCHAR(255)`, Nullable)
* `created_at`, `updated_at` (`TIMESTAMP`)

---

### 2.6. Modul Keuangan: SPP & Kas Madrasah (`tuition_bills`, `tuition_payments`)

#### `tuition_bills`
Tagihan bulanan santri aktif.
* `id` (`BIGINT`, PK, Auto Increment)
* `student_id` (`BIGINT`, FK -> `students.id`, ON DELETE CASCADE)
* `academic_year_id` (`BIGINT`, FK -> `academic_years.id`, ON DELETE CASCADE)
* `bill_title` (`VARCHAR(150)`): Misal `'SPP Bulan September 2026'`.
* `month` (`TINYINT`, Nullable): Indeks bulan (1-12).
* `year` (`YEAR`): Tahun buku tagihan.
* `amount` (`DECIMAL(12, 2)`): Nominal tagihan.
* `status` (`ENUM('unpaid', 'pending_verification', 'paid')`, Default: `'unpaid'`)
* `due_date` (`DATE`): Tanggal jatuh tempo pembayaran.
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `tuition_payments`
Log transaksi pembayaran yang diajukan wali murid dan diverifikasi admin.
* `id` (`BIGINT`, PK, Auto Increment)
* `tuition_bill_id` (`BIGINT`, FK -> `tuition_bills.id`, ON DELETE CASCADE)
* `verified_by` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable): Admin penerima.
* `receipt_number` (`VARCHAR(50)`, Unique): Nomor bukti bayar resmi (cth: `'KW-2026-09-012'`).
* `amount_paid` (`DECIMAL(12, 2)`): Nominal yang dibayarkan.
* `payment_method` (`ENUM('transfer_bank', 'tunai_madrasah')`, Default: `'transfer_bank'`)
* `bank_sender_name` (`VARCHAR(100)`, Nullable): Nama rekening pengirim.
* `proof_attachment` (`VARCHAR(255)`, Nullable): Path foto struk transfer.
* `payment_date` (`DATE`): Tanggal transaksi.
* `verified_at` (`TIMESTAMP`, Nullable): Waktu status disahkan Lunas.
* `created_at`, `updated_at` (`TIMESTAMP`)

---

### 2.7. Modul Konten Publik / Mini CMS (`articles`, `school_galleries`, `announcements`)

#### `articles`
Berita dan artikel kegiatan madrasah.
* `id` (`BIGINT`, PK, Auto Increment)
* `author_id` (`BIGINT`, FK -> `users.id`, ON DELETE SET NULL, Nullable)
* `title` (`VARCHAR(255)`): Judul berita.
* `slug` (`VARCHAR(255)`, Unique): URL ramah SEO.
* `content` (`LONGTEXT`): Isi berita.
* `thumbnail` (`VARCHAR(255)`, Nullable): Gambar utama.
* `is_published` (`BOOLEAN`, Default: `TRUE`)
* `published_at` (`TIMESTAMP`, Nullable)
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `school_galleries`
Galeri dokumentasi kegiatan seni, hadrah, dan kepramukaan.
* `id` (`BIGINT`, PK, Auto Increment)
* `title` (`VARCHAR(200)`): Judul dokumentasi.
* `category` (`ENUM('seni_pentas', 'hadrah_rebana', 'pramuka', 'fasilitas', 'prestasi')`)
* `image_path` (`VARCHAR(255)`): Lokasi file foto.
* `created_at`, `updated_at` (`TIMESTAMP`)

#### `announcements`
Pemberitahuan madrasah.
* `id` (`BIGINT`, PK, Auto Increment)
* `title` (`VARCHAR(200)`): Judul pengumuman.
* `content` (`TEXT`): Rincian pengumuman.
* `target_audience` (`ENUM('public', 'parent', 'all')`, Default: `'all'`)
* `valid_until` (`DATE`, Nullable): Batas masa tayang.
* `created_at`, `updated_at` (`TIMESTAMP`)

---

## 3. SQL Data Definition Language (DDL Migrations)

```sql
-- Nonaktifkan foreign key checks sementara saat setup skema
SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABEL PENGGUNA & AUTENTIKASI
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL UNIQUE,
    `phone_number` VARCHAR(30) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'teacher', 'parent') NOT NULL,
    `avatar_url` VARCHAR(255) NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_phone` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL TAHUN AKADEMIK
DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `semester` ENUM('ganjil', 'genap') NOT NULL,
    `is_active` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL ROMBONGAN BELAJAR (KELAS)
DROP TABLE IF EXISTS `classrooms`;
CREATE TABLE `classrooms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `academic_year_id` BIGINT UNSIGNED NOT NULL,
    `homeroom_teacher_id` BIGINT UNSIGNED NULL,
    `name` VARCHAR(50) NOT NULL,
    `grade_level` TINYINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_classrooms_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_classrooms_homeroom_teacher` FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL MATA PELAJARAN
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `category` ENUM('agama', 'umum', 'muatan_lokal') NOT NULL DEFAULT 'umum',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABEL DATA SANTRI (STUDENTS)
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` BIGINT UNSIGNED NOT NULL,
    `classroom_id` BIGINT UNSIGNED NULL,
    `nis` VARCHAR(30) NULL UNIQUE,
    `nisn` VARCHAR(30) NULL UNIQUE,
    `full_name` VARCHAR(255) NOT NULL,
    `nickname` VARCHAR(100) NULL,
    `gender` ENUM('L', 'P') NOT NULL,
    `birth_place` VARCHAR(100) NOT NULL,
    `birth_date` DATE NOT NULL,
    `address` TEXT NOT NULL,
    `status` ENUM('active', 'graduated', 'transferred', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_students_classroom` (`classroom_id`),
    INDEX `idx_students_parent` (`parent_id`),
    CONSTRAINT `fk_students_parent` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_students_classroom` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABEL PPDB ONLINE
DROP TABLE IF EXISTS `ppdb_registrations`;
CREATE TABLE `ppdb_registrations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `registration_number` VARCHAR(50) NOT NULL UNIQUE,
    `child_name` VARCHAR(255) NOT NULL,
    `nik` VARCHAR(20) NOT NULL,
    `gender` ENUM('L', 'P') NOT NULL,
    `birth_place` VARCHAR(100) NOT NULL,
    `birth_date` DATE NOT NULL,
    `previous_school` VARCHAR(150) NOT NULL,
    `parent_name` VARCHAR(255) NOT NULL,
    `parent_whatsapp` VARCHAR(30) NOT NULL,
    `parent_job` VARCHAR(100) NULL,
    `address` TEXT NOT NULL,
    `file_birth_certificate` VARCHAR(255) NOT NULL,
    `file_family_card` VARCHAR(255) NOT NULL,
    `file_photo` VARCHAR(255) NULL,
    `status` ENUM('pending', 'verified', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    `admin_notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ppdb_status` (`status`),
    INDEX `idx_ppdb_reg_num` (`registration_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TABEL PRESENSI SISWA
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `classroom_id` BIGINT UNSIGNED NOT NULL,
    `recorded_by` BIGINT UNSIGNED NULL,
    `date` DATE NOT NULL,
    `status` ENUM('hadir', 'sakit', 'izin', 'alpa') NOT NULL DEFAULT 'hadir',
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_student_date_attendance` (`student_id`, `date`),
    INDEX `idx_attendances_classroom_date` (`classroom_id`, `date`),
    CONSTRAINT `fk_attendances_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendances_classroom` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_attendances_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. TABEL PENGAJUAN SURAT IZIN / SAKIT
DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE `leave_requests` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `parent_id` BIGINT UNSIGNED NOT NULL,
    `reviewed_by` BIGINT UNSIGNED NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `reason_type` ENUM('sakit', 'izin') NOT NULL,
    `description` TEXT NOT NULL,
    `attachment_path` VARCHAR(255) NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `review_notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_leave_student` (`student_id`),
    INDEX `idx_leave_status` (`status`),
    CONSTRAINT `fk_leave_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_leave_parent` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_leave_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. TABEL SETORAN TAHFIDZ & TAHSIN
DROP TABLE IF EXISTS `tahfidz_records`;
CREATE TABLE `tahfidz_records` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `teacher_id` BIGINT UNSIGNED NULL,
    `date` DATE NOT NULL,
    `surah_name` VARCHAR(100) NOT NULL,
    `verse_range` VARCHAR(50) NOT NULL,
    `fluency_grade` ENUM('lancar', 'kurang_lancar', 'mengulang') NOT NULL,
    `tajweed_grade` ENUM('mumtaz', 'jayyid', 'maqbul') NOT NULL,
    `audio_recording_path` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_tahfidz_student_date` (`student_id`, `date`),
    CONSTRAINT `fk_tahfidz_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tahfidz_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. TABEL NILAI AKADEMIK
DROP TABLE IF EXISTS `academic_grades`;
CREATE TABLE `academic_grades` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `classroom_id` BIGINT UNSIGNED NOT NULL,
    `subject_id` BIGINT UNSIGNED NOT NULL,
    `academic_year_id` BIGINT UNSIGNED NOT NULL,
    `teacher_id` BIGINT UNSIGNED NULL,
    `grade_type` ENUM('harian', 'tugas', 'pts', 'pas') NOT NULL,
    `score` DECIMAL(5, 2) NOT NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_grades_student_subject` (`student_id`, `subject_id`),
    CONSTRAINT `fk_grades_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grades_classroom` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grades_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grades_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_grades_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. TABEL TAGIHAN SPP & IURAN
DROP TABLE IF EXISTS `tuition_bills`;
CREATE TABLE `tuition_bills` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `academic_year_id` BIGINT UNSIGNED NOT NULL,
    `bill_title` VARCHAR(150) NOT NULL,
    `month` TINYINT UNSIGNED NULL,
    `year` YEAR NOT NULL,
    `amount` DECIMAL(12, 2) NOT NULL,
    `status` ENUM('unpaid', 'pending_verification', 'paid') NOT NULL DEFAULT 'unpaid',
    `due_date` DATE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_tuition_student_status` (`student_id`, `status`),
    CONSTRAINT `fk_tuition_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tuition_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. TABEL PEMBAYARAN & KWITANSI SPP
DROP TABLE IF EXISTS `tuition_payments`;
CREATE TABLE `tuition_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tuition_bill_id` BIGINT UNSIGNED NOT NULL,
    `verified_by` BIGINT UNSIGNED NULL,
    `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
    `amount_paid` DECIMAL(12, 2) NOT NULL,
    `payment_method` ENUM('transfer_bank', 'tunai_madrasah') NOT NULL DEFAULT 'transfer_bank',
    `bank_sender_name` VARCHAR(100) NULL,
    `proof_attachment` VARCHAR(255) NULL,
    `payment_date` DATE NOT NULL,
    `verified_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_payment_tuition_bill` FOREIGN KEY (`tuition_bill_id`) REFERENCES `tuition_bills` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_payment_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. TABEL ARTIKEL & BERITA (CMS)
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `author_id` BIGINT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `thumbnail` VARCHAR(255) NULL,
    `is_published` BOOLEAN NOT NULL DEFAULT TRUE,
    `published_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_articles_slug` (`slug`),
    CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. TABEL GALERI FOTO KEGIATAN MADRASAH
DROP TABLE IF EXISTS `school_galleries`;
CREATE TABLE `school_galleries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `category` ENUM('seni_pentas', 'hadrah_rebana', 'pramuka', 'fasilitas', 'prestasi') NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_galleries_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. TABEL PENGUMUMAN MADRASAH
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `target_audience` ENUM('public', 'parent', 'all') NOT NULL DEFAULT 'all',
    `valid_until` DATE NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Aktifkan kembali pengecekan foreign key
SET FOREIGN_KEY_CHECKS = 1;
```