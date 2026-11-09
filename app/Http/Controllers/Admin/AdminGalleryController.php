<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\GalleryCategory;
use App\Http\Controllers\Controller;
use App\Models\SchoolGallery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminGalleryController extends Controller
{
    public function index(): View
    {
        $galleries = SchoolGallery::latest()->paginate(15);
        $categories = GalleryCategory::cases();

        return view('admin.galleries.index', compact('galleries', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'in:seni_pentas,hadrah_rebana,pramuka,fasilitas,prestasi'],
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        SchoolGallery::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'image_path' => 'storage/' . $path,
        ]);

        return redirect()->route('admin.galleries.index')
            ->with('success', "Foto galeri \"{$validated['title']}\" berhasil diunggah.");
    }

    public function destroy(SchoolGallery $gallery): RedirectResponse
    {
        $title = $gallery->title;
        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', "Foto galeri \"{$title}\" berhasil dihapus.");
    }
}
