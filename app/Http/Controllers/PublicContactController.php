<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\PrayerTimeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicContactController extends Controller
{
    public function __construct(
        protected PrayerTimeService $prayerTimeService
    ) {}

    public function index(): View
    {
        $prayerSchedule = $this->prayerTimeService->getSchedule();

        $contactInfo = [
            'name' => config('simad.school_name', 'MI Plus Raudlatul Muta\'allimin'),
            'address' => config('simad.school_address', 'Kalipucung, Sanankulon, Blitar, Jawa Timur'),
            'full_address' => 'Jl. KH. Muta\'allim No. 09, Desa Kalipucung, Kec. Sanankulon, Kab. Blitar, Jawa Timur 66173',
            'phone' => config('simad.contact_phone', '081234567890'),
            'email' => config('simad.contact_email', 'info@miplus-kalipucung.sch.id'),
            'nsm' => config('simad.nsm', '111235050000'),
            'npsn' => config('simad.npsn', '60700000'),
            'coordinates' => config('simad.blitar_coordinates', [
                'latitude' => -8.0983,
                'longitude' => 112.1681,
            ]),
            'office_hours' => [
                ['days' => 'Senin - Kamis', 'time' => '07.00 - 14.30 WIB'],
                ['days' => 'Jum\'at', 'time' => '07.00 - 11.00 WIB'],
                ['days' => 'Sabtu', 'time' => '07.00 - 13.00 WIB'],
                ['days' => 'Ahad & Hari Libur', 'time' => 'Tutup (Hotline WA Tetap Aktif)'],
            ],
        ];

        return view('public.contact', compact('prayerSchedule', 'contactInfo'));
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'category' => ['required', 'string', 'in:ppdb,kurikulum,administrasi,umum'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $categoryLabel = match ($validated['category']) {
            'ppdb' => 'Konsultasi PPDB Online',
            'kurikulum' => 'Informasi Kurikulum & Program Madrasah',
            'administrasi' => 'Administrasi / Mutasi Siswa',
            default => 'Pertanyaan Umum',
        };

        ContactMessage::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'category' => $validated['category'],
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        return back()->with('success', "Alhamdulillah, terima kasih {$validated['name']}. Pesan terkait {$categoryLabel} telah kami terima. Tim Tata Usaha akan segera menghubungi Anda melalui nomor WhatsApp yang terdaftar.");
    }
}
