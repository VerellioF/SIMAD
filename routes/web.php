<?php

declare(strict_types=1);

use App\Exports\PpdbEmisExport;
use App\Http\Controllers\Admin\AdminAgendaController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminClassroomController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExtracurricularController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Admin\AdminPpdbController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\AdminTuitionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Parent\ParentAttendanceController;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentExtracurricularController;
use App\Http\Controllers\Parent\ParentGradeController;
use App\Http\Controllers\Parent\ParentLeaveRequestController;
use App\Http\Controllers\Parent\ParentScheduleController;
use App\Http\Controllers\Parent\ParentTahfidzController;
use App\Http\Controllers\Parent\ParentTuitionController;
use App\Http\Controllers\Parent\ParentWorshipController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\PublicCalendarController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicExtracurricularController;
use App\Http\Controllers\PublicTeacherController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Teacher\LeaveRequestController;
use App\Http\Controllers\Teacher\TahfidzController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherScheduleController;
use App\Http\Controllers\Teacher\TeacherWorshipController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

// 1. Halaman Publik & Profil Madrasah
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/tentang/guru', [PublicTeacherController::class, 'index'])->name('public.teachers');
Route::get('/kalender', [PublicCalendarController::class, 'index'])->name('public.calendar');
Route::get('/ekstrakurikuler', [PublicExtracurricularController::class, 'index'])->name('public.ekskul');

// 2. Modul PPDB Online (Publik)
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PpdbController::class, 'create'])->name('register');
    Route::post('/daftar', [PpdbController::class, 'store'])->name('store');
    Route::get('/sukses/{registration_number}', [PpdbController::class, 'success'])->name('success');
    Route::get('/cek-status', [PpdbController::class, 'checkStatus'])->name('check_status');
    Route::get('/cetak/{registration_number}', [PpdbController::class, 'downloadProof'])->name('print_proof');
});

// 3. Autentikasi Sistem Terpadu
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shortcut URL Login Portal
Route::get('/admin/login', fn () => redirect()->route('login', ['portal' => 'admin']));
Route::get('/guru/login', fn () => redirect()->route('login', ['portal' => 'teacher']));
Route::get('/wali/login', fn () => redirect()->route('login', ['portal' => 'parent']));

// 4. Custom Portal Admin / TU
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/ppdb', [AdminPpdbController::class, 'index'])->name('ppdb.index');
    Route::get('/ppdb/{registration}', [AdminPpdbController::class, 'show'])->name('ppdb.show');
    Route::post('/ppdb/{registration}/status', [AdminPpdbController::class, 'updateStatus'])->name('ppdb.update_status');
    Route::get('/export/ppdb-emis', fn () => Excel::download(new PpdbEmisExport, 'data-ppdb-emis.xlsx'))->name('ppdb.export_emis');

    // Keuangan & SPP (Fase 3)
    Route::get('/spp', [AdminTuitionController::class, 'index'])->name('tuition.index');
    Route::post('/spp/generate', [AdminTuitionController::class, 'generateMonthlyBills'])->name('tuition.generate');
    Route::post('/spp/{bill}/verify', [AdminTuitionController::class, 'verifyPayment'])->name('tuition.verify');
    Route::post('/spp/{bill}/cash', [AdminTuitionController::class, 'recordCashPayment'])->name('tuition.cash');

    // Master Data (Fase 4)
    Route::get('/santri', [AdminStudentController::class, 'index'])->name('students.index');
    Route::post('/santri', [AdminStudentController::class, 'store'])->name('students.store');
    Route::put('/santri/{student}', [AdminStudentController::class, 'update'])->name('students.update');
    Route::delete('/santri/{student}', [AdminStudentController::class, 'destroy'])->name('students.destroy');

    Route::get('/guru', [AdminTeacherController::class, 'index'])->name('teachers.index');
    Route::post('/guru', [AdminTeacherController::class, 'store'])->name('teachers.store');
    Route::put('/guru/{teacher}', [AdminTeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/guru/{teacher}', [AdminTeacherController::class, 'destroy'])->name('teachers.destroy');

    Route::get('/kelas', [AdminClassroomController::class, 'index'])->name('classrooms.index');
    Route::post('/kelas', [AdminClassroomController::class, 'store'])->name('classrooms.store');
    Route::put('/kelas/{classroom}', [AdminClassroomController::class, 'update'])->name('classrooms.update');
    Route::delete('/kelas/{classroom}', [AdminClassroomController::class, 'destroy'])->name('classrooms.destroy');

    // Mini CMS & Publikasi (Fase 4)
    Route::get('/berita', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::post('/berita', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::put('/berita/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/berita/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/pengumuman', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/pengumuman', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/pengumuman/{announcement}', [AdminAnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/pengumuman/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/galeri', [AdminGalleryController::class, 'index'])->name('galleries.index');
    Route::post('/galeri', [AdminGalleryController::class, 'store'])->name('galleries.store');
    Route::delete('/galeri/{gallery}', [AdminGalleryController::class, 'destroy'])->name('galleries.destroy');

    // Modul Tambahan: Agenda, Jadwal & Ekskul
    Route::get('/agenda', [AdminAgendaController::class, 'index'])->name('agenda.index');
    Route::post('/agenda', [AdminAgendaController::class, 'store'])->name('agenda.store');
    Route::put('/agenda/{event}', [AdminAgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{event}', [AdminAgendaController::class, 'destroy'])->name('agenda.destroy');

    Route::get('/jadwal', [AdminScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/jadwal', [AdminScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/jadwal/{schedule}', [AdminScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/jadwal/{schedule}', [AdminScheduleController::class, 'destroy'])->name('schedules.destroy');

    Route::get('/ekskul', [AdminExtracurricularController::class, 'index'])->name('ekskul.index');
    Route::post('/ekskul', [AdminExtracurricularController::class, 'store'])->name('ekskul.store');
    Route::put('/ekskul/{ekskul}', [AdminExtracurricularController::class, 'update'])->name('ekskul.update');
    Route::delete('/ekskul/{ekskul}', [AdminExtracurricularController::class, 'destroy'])->name('ekskul.destroy');
    Route::post('/ekskul/peserta', [AdminExtracurricularController::class, 'enrollStudent'])->name('ekskul.enroll');
    Route::delete('/ekskul/peserta/{enrollment}', [AdminExtracurricularController::class, 'removeStudent'])->name('ekskul.unenroll');
});

// 5. Custom Portal Guru & Wali Kelas
Route::prefix('guru')->middleware(['auth', 'role:teacher'])->name('teacher.')->group(function () {
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');

    // Presensi & Izin (Fase 2)
    Route::get('/presensi', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/presensi', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/izin', [LeaveRequestController::class, 'index'])->name('leave.index');
    Route::match(['POST', 'PUT'], '/izin/{leaveRequest}', [LeaveRequestController::class, 'update'])->name('leave.update');

    // Tahfidz & Nilai (Fase 3)
    Route::get('/tahfidz', [TahfidzController::class, 'index'])->name('tahfidz.index');
    Route::post('/tahfidz', [TahfidzController::class, 'store'])->name('tahfidz.store');
    Route::get('/nilai', [GradeController::class, 'index'])->name('grades.index');
    Route::post('/nilai', [GradeController::class, 'store'])->name('grades.store');

    // Jadwal Mengajar & Mutaba'ah Ibadah Santri
    Route::get('/jadwal', [TeacherScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/mutabaah', [TeacherWorshipController::class, 'index'])->name('worship.index');
    Route::post('/mutabaah/catatan', [TeacherWorshipController::class, 'updateNotes'])->name('worship.notes');
});

// 6. Custom Portal Wali Murid
Route::prefix('wali')->middleware(['auth', 'role:parent'])->name('parent.')->group(function () {
    Route::get('/', [ParentDashboardController::class, 'index'])->name('dashboard');

    // Presensi & Izin (Fase 2)
    Route::get('/presensi', [ParentAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/izin', [ParentLeaveRequestController::class, 'index'])->name('leave.index');
    Route::post('/izin', [ParentLeaveRequestController::class, 'store'])->name('leave.store');

    // Tahfidz, Nilai & SPP (Fase 3)
    Route::get('/tahfidz', [ParentTahfidzController::class, 'index'])->name('tahfidz.index');
    Route::get('/nilai', [ParentGradeController::class, 'index'])->name('grades.index');
    Route::get('/spp', [ParentTuitionController::class, 'index'])->name('tuition.index');
    Route::post('/spp/{bill}/konfirmasi', [ParentTuitionController::class, 'confirmPayment'])->name('tuition.confirm');
    Route::get('/spp/{bill}/kwitansi', [ParentTuitionController::class, 'receipt'])->name('tuition.receipt');

    // Jadwal Pelajaran, Mutaba'ah Ibadah & Ekstrakurikuler
    Route::get('/jadwal', [ParentScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/mutabaah', [ParentWorshipController::class, 'index'])->name('worship.index');
    Route::post('/mutabaah', [ParentWorshipController::class, 'store'])->name('worship.store');
    Route::get('/ekskul', [ParentExtracurricularController::class, 'index'])->name('ekskul.index');
    Route::post('/ekskul/daftar', [ParentExtracurricularController::class, 'enroll'])->name('ekskul.enroll');
    Route::delete('/ekskul/{enrollment}', [ParentExtracurricularController::class, 'cancel'])->name('ekskul.cancel');
});
