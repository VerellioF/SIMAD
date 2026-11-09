<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminClassroomController extends Controller
{
    public function index(): View
    {
        $classrooms = Classroom::with(['academicYear', 'homeroomTeacher'])
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $activeYear = AcademicYear::where('is_active', true)->first();
        $teachers = User::where('role', UserRole::Teacher)->orderBy('name')->get();

        return view('admin.classrooms.index', compact('classrooms', 'activeYear', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade_level' => ['required', 'integer', 'between:1,6'],
            'homeroom_teacher_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', UserRole::Teacher->value),
            ],
        ], [
            'homeroom_teacher_id.exists' => 'Akun yang dipilih harus berstatus sebagai Guru.',
        ]);

        Classroom::create([
            'academic_year_id' => $activeYear->id,
            'name' => $validated['name'],
            'grade_level' => $validated['grade_level'],
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? null,
        ]);

        return redirect()->route('admin.classrooms.index')
            ->with('success', "Rombel {$validated['name']} berhasil dibuat.");
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'grade_level' => ['required', 'integer', 'between:1,6'],
            'homeroom_teacher_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', UserRole::Teacher->value),
            ],
        ], [
            'homeroom_teacher_id.exists' => 'Akun yang dipilih harus berstatus sebagai Guru.',
        ]);

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.index')
            ->with('success', "Rombel {$classroom->name} berhasil diperbarui.");
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        if ($classroom->students()->count() > 0) {
            return back()->with('error', "Rombel {$classroom->name} tidak dapat dihapus karena masih memiliki siswa aktif.");
        }

        $name = $classroom->name;
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
            ->with('success', "Rombel {$name} berhasil dihapus.");
    }
}
