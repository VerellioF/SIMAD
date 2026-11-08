<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        $query = SchoolEvent::query()
            ->with('academicYear')
            ->orderBy('start_date');

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('event_type', $request->type);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        $events = $query->paginate(15)->withQueryString();

        $upcomingEvents = SchoolEvent::query()
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        return view('public.calendar', compact('events', 'upcomingEvents', 'activeYear'));
    }
}
