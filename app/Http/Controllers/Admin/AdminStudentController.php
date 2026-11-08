<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'status' => ['required', 'in:active,graduated,transferred,inactive'],
        ], [
            'nis.unique' => 'NIS ini sudah terdaftar untuk siswa lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar untuk siswa lain.',
        ]);

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
            'status' => ['required', 'in:active,graduated,transferred,inactive'],
        ]);

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
}
