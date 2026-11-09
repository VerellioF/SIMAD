<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use App\Services\ReportCardPdfService;
use App\Services\StudentCardPdfService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $classroomId = $request->query('classroom_id');
        $status = $request->query('status');

        $query = Student::with(['classroom', 'parent'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $students = $query->paginate(15)->withQueryString();
        $classrooms = Classroom::orderBy('name')->get();
        $parents = User::where('role', UserRole::Parent)->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classrooms', 'parents', 'search', 'classroomId', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:L,P'],
            'nis' => ['required', 'string', 'max:30', 'unique:students,nis'],
            'nisn' => ['required', 'string', 'max:30', 'unique:students,nisn'],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'parent_id' => ['required', 'exists:users,id'],
            'address' => ['required', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status' => ['required', 'in:active,graduated,transferred,inactive'],
        ], [
            'nis.unique' => 'NIS ini sudah terdaftar untuk siswa lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar untuk siswa lain.',
            'photo.image' => 'File pasfoto harus berupa format gambar (JPG/PNG).',
            'photo.max' => 'Ukuran file pasfoto maksimal 2MB.',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('students', 'public');
        }
        unset($validated['photo']);

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', "Siswa {$validated['full_name']} berhasil ditambahkan ke sistem.");
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:L,P'],
            'nis' => ['required', 'string', 'max:30', Rule::unique('students', 'nis')->ignore($student->id)],
            'nisn' => ['required', 'string', 'max:30', Rule::unique('students', 'nisn')->ignore($student->id)],
            'birth_place' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'parent_id' => ['required', 'exists:users,id'],
            'address' => ['required', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status' => ['required', 'in:active,graduated,transferred,inactive'],
        ], [
            'photo.image' => 'File pasfoto harus berupa format gambar (JPG/PNG).',
            'photo.max' => 'Ukuran file pasfoto maksimal 2MB.',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('students', 'public');
        }
        unset($validated['photo']);

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', "Data siswa {$student->full_name} berhasil diperbarui.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $name = $student->full_name;
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', "Data siswa {$name} berhasil dihapus dari sistem.");
    }

    public function downloadReportCard(Student $student, ReportCardPdfService $service): Response
    {
        $pdf = $service->generate($student);
        return $pdf->stream("Rapor_{$student->nis}_{$student->full_name}.pdf");
    }

    public function downloadStudentCard(Student $student, StudentCardPdfService $service): Response
    {
        $pdf = $service->generateStudentCard($student);
        return $pdf->stream("KTS_{$student->nis}_{$student->full_name}.pdf");
    }

    public function downloadExamCard(Student $student, StudentCardPdfService $service): Response
    {
        $pdf = $service->generateExamCard($student);
        return $pdf->stream("Kartu_Ujian_{$student->nis}_{$student->full_name}.pdf");
    }

    public function downloadClassExamCards(Classroom $classroom, StudentCardPdfService $service): Response
    {
        $pdf = $service->generateClassExamCards($classroom);
        return $pdf->stream("Kartu_Ujian_Kelas_{$classroom->name}.pdf");
    }
}
