<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\View;

class PublicTeacherController extends Controller
{
    public function index(): View
    {
        $leadership = User::query()
            ->where('role', UserRole::Admin)
            ->orWhere(function ($q) {
                $q->where('role', UserRole::Teacher)
                    ->where('position', 'like', '%Kepala%');
            })
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $teachers = User::query()
            ->where('role', UserRole::Teacher)
            ->where(function ($q) {
                $q->whereNull('position')
                    ->orWhere('position', 'not like', '%Kepala%');
            })
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('public.teachers', compact('leadership', 'teachers'));
    }
}
