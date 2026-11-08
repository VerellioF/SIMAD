<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AnnouncementTarget;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::latest()->paginate(15);
        $targets = AnnouncementTarget::cases();

        return view('admin.announcements.index', compact('announcements', 'targets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'target_audience' => ['required', 'in:public,parent,all'],
            'valid_until' => ['nullable', 'date'],
        ]);

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', "Pengumuman \"{$validated['title']}\" berhasil dipublikasikan.");
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'target_audience' => ['required', 'in:public,parent,all'],
            'valid_until' => ['nullable', 'date'],
        ]);

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', "Pengumuman \"{$announcement->title}\" berhasil diperbarui.");
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $title = $announcement->title;
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', "Pengumuman \"{$title}\" berhasil dihapus.");
    }
}
