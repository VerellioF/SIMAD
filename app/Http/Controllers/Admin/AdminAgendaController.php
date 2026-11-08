<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAgendaController extends Controller
{
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('is_active')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $query = SchoolEvent::query()->with('academicYear')->latest('start_date');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('event_type', $request->type);
        }

        $events = $query->paginate(15)->withQueryString();

        return view('admin.agenda.index', compact('events', 'academicYears', 'activeYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'title' => ['required', 'string', 'max:150'],
            'event_type' => ['required', 'in:akademik,ujian,libur,kegiatan,phbi'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_holiday' => ['nullable', 'boolean'],
        ]);

        $validated['end_date'] = $validated['end_date'] ?? $validated['start_date'];
        $validated['is_holiday'] = $request->boolean('is_holiday');

        SchoolEvent::create($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda madrasah berhasil ditambahkan.');
    }

    public function update(Request $request, SchoolEvent $event): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'title' => ['required', 'string', 'max:150'],
            'event_type' => ['required', 'in:akademik,ujian,libur,kegiatan,phbi'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_holiday' => ['nullable', 'boolean'],
        ]);

        $validated['end_date'] = $validated['end_date'] ?? $validated['start_date'];
        $validated['is_holiday'] = $request->boolean('is_holiday');

        $event->update($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda madrasah berhasil diperbarui.');
    }

    public function destroy(SchoolEvent $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda madrasah berhasil dihapus.');
    }
}
