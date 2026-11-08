@props([
    'currentPanel' => 'admin',
])

<div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 space-y-4">
    <!-- Quick Portal Switcher -->
    <div>
        <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-center mb-2">
            Pindah Portal Sistem
        </p>
        <div class="grid grid-cols-3 gap-2 text-center text-xs">
            <a href="/admin/login" class="px-2.5 py-1.5 rounded-lg border {{ $currentPanel === 'admin' ? 'bg-emerald-800 text-white border-emerald-900 font-bold' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Admin / TU
            </a>
            <a href="/guru/login" class="px-2.5 py-1.5 rounded-lg border {{ $currentPanel === 'teacher' ? 'bg-teal-700 text-white border-teal-800 font-bold' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Guru Kelas
            </a>
            <a href="/wali/login" class="px-2.5 py-1.5 rounded-lg border {{ $currentPanel === 'parent' ? 'bg-amber-600 text-white border-amber-700 font-bold' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                Wali Murid
            </a>
        </div>
    </div>

    <!-- Demo Account Helper Box -->
    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-3 border border-slate-200/80 dark:border-slate-800 text-left text-[11px] text-slate-500 dark:text-slate-400 space-y-1">
        <div class="flex items-center justify-between font-semibold text-slate-700 dark:text-slate-300">
            <span>Akun Uji Coba (Demo):</span>
            <span class="text-[10px] bg-slate-200 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">Default Password: password</span>
        </div>
        @if($currentPanel === 'admin')
            <p>Email: <code class="text-emerald-700 dark:text-emerald-400 font-bold">admin@simad.id</code></p>
        @elseif($currentPanel === 'teacher')
            <p>Email: <code class="text-teal-700 dark:text-teal-400 font-bold">guru@simad.id</code></p>
        @elseif($currentPanel === 'parent')
            <p>Email: <code class="text-amber-700 dark:text-amber-400 font-bold">wali@simad.id</code></p>
        @endif
    </div>
</div>
