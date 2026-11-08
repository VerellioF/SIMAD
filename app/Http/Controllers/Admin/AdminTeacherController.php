<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminTeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = User::where('role', UserRole::Teacher)
            ->with('homeroomClassrooms')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $teachers = $query->paginate(15)->withQueryString();
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.teachers.index', compact('teachers', 'classrooms', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'homeroom_classroom_id' => ['nullable', 'exists:classrooms,id'],
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Teacher,
            'is_active' => true,
        ]);

        if (! empty($validated['homeroom_classroom_id'])) {
            Classroom::where('id', $validated['homeroom_classroom_id'])->update([
                'homeroom_teacher_id' => $teacher->id,
            ]);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', "Akun guru {$teacher->name} berhasil ditambahkan.");
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->isTeacher(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
            'homeroom_classroom_id' => ['nullable', 'exists:classrooms,id'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'is_active' => (bool) $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $teacher->update($updateData);

        // Reset previous homeroom assignment
        Classroom::where('homeroom_teacher_id', $teacher->id)->update(['homeroom_teacher_id' => null]);

        if (! empty($validated['homeroom_classroom_id'])) {
            Classroom::where('id', $validated['homeroom_classroom_id'])->update([
                'homeroom_teacher_id' => $teacher->id,
            ]);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', "Data ustadz/ustadzah {$teacher->name} berhasil diperbarui.");
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($teacher->isTeacher(), 404);

        // Remove homeroom teacher link before deleting
        Classroom::where('homeroom_teacher_id', $teacher->id)->update(['homeroom_teacher_id' => null]);
        $name = $teacher->name;
        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', "Akun guru {$name} berhasil dihapus.");
    }
}
