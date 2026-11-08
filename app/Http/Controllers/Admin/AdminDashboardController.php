<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PpdbStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\PpdbRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_ppdb' => PpdbRegistration::count(),
            'pending_ppdb' => PpdbRegistration::where('status', PpdbStatus::Pending)->count(),
            'verified_ppdb' => PpdbRegistration::where('status', PpdbStatus::Verified)->count(),
            'accepted_ppdb' => PpdbRegistration::where('status', PpdbStatus::Accepted)->count(),
            'total_students' => Student::count(),
            'total_teachers' => User::where('role', UserRole::Teacher)->count(),
            'total_classrooms' => Classroom::count(),
        ];

        $latestRegistrations = PpdbRegistration::latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestRegistrations'));
    }
}
