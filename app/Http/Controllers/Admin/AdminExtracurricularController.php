<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\ExtracurricularEnrollment;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminExtracurricularController extends Controller
{
    public function index(Request $request): View
    {
        $extracurriculars = Extracurricular::withCount(['enrollments' => function ($q) {
            $q->where('status', 'active');
        }])->latest()->paginate(10);

        $selectedEkskul = null;
        $enrollments = collect();
        if ($request->filled('ekskul_id')) {
            $selectedEkskul = Extracurricular::find($request->ekskul_id);
            if ($selectedEkskul) {
                $enrollments = ExtracurricularEnrollment::with(['student.classroom', 'academicYear'])
                    ->where('extracurricular_id', $selectedEkskul->id)
                    ->latest()
                    ->get();
            }
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $students = Student::with('classroom')->where('status', 'active')->orderBy('full_name')->get();

        return view('admin.extracurriculars.index', compact('extracurriculars', 'selectedEkskul', 'enrollments', 'activeYear', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:keagamaan,seni_budaya,olahraga,kepanduan,sains'],
            'instructor_name' => ['required', 'string', 'max:150'],
            'schedule_day' => ['required', 'string', 'max:30'],
            'schedule_time' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['location'] = $validated['location'] ?? 'Madrasah';

        Extracurricular::create($validated);

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function update(Request $request, Extracurricular $ekskul): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:keagamaan,seni_budaya,olahraga,kepanduan,sains'],
            'instructor_name' => ['required', 'string', 'max:150'],
            'schedule_day' => ['required', 'string', 'max:30'],
            'schedule_time' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['location'] = $validated['location'] ?? 'Madrasah';

        $ekskul->update($validated);

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(Extracurricular $ekskul): RedirectResponse
    {
        $ekskul->delete();

        return redirect()->route('admin.ekskul.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }

    public function enrollStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'extracurricular_id' => ['required', 'exists:extracurriculars,id'],
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        ExtracurricularEnrollment::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'extracurricular_id' => $validated['extracurricular_id'],
                'academic_year_id' => $validated['academic_year_id'],
            ],
            [
                'status' => 'active',
            ]
        );

        return redirect()->route('admin.ekskul.index', ['ekskul_id' => $validated['extracurricular_id']])
            ->with('success', 'Santri berhasil didaftarkan ke ekstrakurikuler.');
    }

    public function removeStudent(ExtracurricularEnrollment $enrollment): RedirectResponse
    {
        $ekskulId = $enrollment->extracurricular_id;
        $enrollment->delete();

        return redirect()->route('admin.ekskul.index', ['ekskul_id' => $ekskulId])
            ->with('success', 'Pendaftaran santri berhasil dibatalkan.');
    }
}
