@extends('layouts.public')

@section('title', 'Kontak & Lokasi Madrasah - ' . config('simad.school_name'))

@section('content')
<!-- Hero Section -->
<section class="relative bg-emerald-950 text-white py-16 sm:py-20 pattern-overlay overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gold-500/20 text-gold-400 border border-gold-500/30 mb-4">
            Layanan Terpadu & Informasi Resmi
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
            Kontak & Lokasi Madrasah
        </h1>
        <p class="text-emerald-200 text-sm sm:text-base max-w-2xl mx-auto mt-3 leading-relaxed">
            Pusat layanan informasi PPDB, konsultasi program kurikulum tahfidz, administrasi tata usaha, serta lokasi kampus MI Plus Raudlatul Muta'allimin Kalipucung Blitar.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-start gap-3 shadow-sm">
            <svg class="w-6 h-6 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong class="block font-bold">Pesan Terkirim!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- 4 Kartu Informasi Kontak Cepat -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Alamat Kampus -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-700 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-850 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Alamat Kampus</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $contactInfo['full_address'] }}
                </p>
            </div>
            <a href="https://maps.google.com/?q={{ $contactInfo['coordinates']['latitude'] }},{{ $contactInfo['coordinates']['longitude'] }}"
               target="_blank" class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-950">
                <span>Buka Petunjuk Arah</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- Hotline WhatsApp TU -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-700 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-850 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Hotline & WhatsApp</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-mono">
                    {{ $contactInfo['phone'] }}
                </p>
                <p class="text-[11px] text-slate-400">Respon cepat jam kerja operasional madrasah.</p>
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactInfo['phone']) }}?text=Assalamu'alaikum%20Warahmatullah,%20saya%20ingin%20berkonsultasi%20mengenai%20MI%20Plus%20Raudlatul%20Muta'allimin"
               target="_blank" class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-teal-800 hover:text-teal-950">
                <span>Chat WhatsApp Admin</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- Surat & Email Resmi -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-700 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-850 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Email Resmi</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-mono truncate">
                    {{ $contactInfo['email'] }}
                </p>
                <p class="text-[11px] text-slate-400">Untuk surat kedinasan, proposal & kemitraan.</p>
            </div>
            <a href="mailto:{{ $contactInfo['email'] }}"
               class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 hover:text-amber-950">
                <span>Kirim Surel Resmi</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- Jam Pelayanan Tata Usaha -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-700 transition">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-850 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Jam Layanan TU</h3>
                <div class="space-y-1 text-xs text-slate-600">
                    @foreach($contactInfo['office_hours'] as $oh)
                        <div class="flex justify-between text-[11px]">
                            <span class="font-semibold">{{ $oh['days'] }}:</span>
                            <span class="font-mono text-slate-500">{{ $oh['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <span class="mt-4 text-[11px] text-emerald-800 font-bold">Tatap Muka & Administrasi</span>
        </div>
    </div>

    <!-- Peta Lokasi & Form Konsultasi Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Kolom Peta Interaktif (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Peta Lokasi & Rute Kampus</h2>
                    <p class="text-xs text-slate-500">Desa Kalipucung, Kecamatan Sanankulon, Kabupaten Blitar</p>
                </div>
                <a href="https://maps.google.com/?q={{ $contactInfo['coordinates']['latitude'] }},{{ $contactInfo['coordinates']['longitude'] }}"
                   target="_blank"
                   class="px-4 py-2 rounded-xl bg-emerald-950 text-white font-bold text-xs hover:bg-emerald-900 transition flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Google Maps</span>
                </a>
            </div>

            <!-- Google Maps Iframe Embed -->
            <div class="w-full h-80 sm:h-96 bg-slate-100 relative">
                <iframe
                    src="https://maps.google.com/maps?q={{ $contactInfo['coordinates']['latitude'] }},{{ $contactInfo['coordinates']['longitude'] }}&hl=id&z=15&output=embed"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Peta Lokasi MI Plus Raudlatul Muta'allimin Kalipucung Blitar">
                </iframe>
            </div>

            <!-- Panduan Rute Menuju Lokasi -->
            <div class="p-6 bg-slate-50 border-t border-slate-100 space-y-3">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Petunjuk Akses Kendaraan:</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600">
                    <div class="p-3 bg-white rounded-2xl border border-slate-200/80">
                        <strong class="text-slate-800 block mb-1">Dari Arah Kota Blitar:</strong>
                        Menuju barat ke arah Sanankulon via Jl. Raya Kalipucung (± 15 menit). Di perempatan Kalipucung belok ke utara 500 meter.
                    </div>
                    <div class="p-3 bg-white rounded-2xl border border-slate-200/80">
                        <strong class="text-slate-800 block mb-1">Dari Arah Srengat / Kediri:</strong>
                        Melintasi Jl. Raya Blitar-Kediri via Sanankulon, belok kanan di gapura Kalipucung. Kampus berada dekat Masjid Jami' Raudlatul Muta'allimin.
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Form Konsultasi & Pesan (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div>
                <span class="text-[10px] font-bold text-gold-600 uppercase tracking-wider bg-gold-50 px-2.5 py-1 rounded-full border border-gold-200">
                    Formulir Konsultasi
                </span>
                <h2 class="text-xl font-black text-slate-900 mt-2">Kirim Pesan ke Tata Usaha</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Ajukan pertanyaan seputar PPDB, kurikulum tahfidz, atau permohonan kunjungan madrasah.
                </p>
            </div>

            <form action="{{ route('public.contact.send') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap Ayah / Bunda</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           placeholder="Contoh: Muhammad Ramadhan"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800 bg-slate-50/50">
                    @error('name')<span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800 bg-slate-50/50">
                    @error('phone')<span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Pertanyaan</label>
                    <select name="category" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-800 bg-slate-50/50">
                        <option value="ppdb" {{ old('category') === 'ppdb' ? 'selected' : '' }}>Konsultasi PPDB & Pendaftaran Siswa Baru</option>
                        <option value="kurikulum" {{ old('category') === 'kurikulum' ? 'selected' : '' }}>Informasi Kurikulum Terpadu & Tahfidz</option>
                        <option value="administrasi" {{ old('category') === 'administrasi' ? 'selected' : '' }}>Administrasi & Mutasi Masuk/Keluar</option>
                        <option value="umum" {{ old('category') === 'umum' ? 'selected' : '' }}>Pertanyaan Kemitraan / Umum</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Isi Pesan / Pertanyaan</label>
                    <textarea name="message" rows="4" required
                              placeholder="Tuliskan pertanyaan atau kebutuhan konsultasi Anda secara lengkap..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-800 bg-slate-50/50">{{ old('message') }}</textarea>
                    @error('message')<span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>@enderror
                </div>

                <button type="submit"
                        class="w-full py-3 px-6 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Kirim Pesan Konsultasi</span>
                </button>
            </form>
        </div>
    </div>

</section>
@endsection
