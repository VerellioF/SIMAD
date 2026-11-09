<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $portal = $request->query('portal', 'admin');

        return view('auth.login', compact('portal'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau Nomor WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Determine if login input is email or phone number
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        if ($field === 'phone_number') {
            $cleaned = preg_replace('/[^0-9]/', '', $loginInput);
            $variants = array_unique(array_filter([
                $loginInput,
                $cleaned,
                str_starts_with($cleaned, '08') ? ('628' . substr($cleaned, 2)) : null,
                str_starts_with($cleaned, '628') ? ('08' . substr($cleaned, 3)) : null,
            ]));

            $authenticated = false;
            foreach ($variants as $phone) {
                if (Auth::attempt(['phone_number' => $phone, 'password' => $password], $remember)) {
                    $authenticated = true;
                    break;
                }
            }

            if (! $authenticated) {
                return back()->withInput($request->only('login', 'remember'))
                    ->withErrors(['login' => 'Email / Nomor WhatsApp atau Password salah.']);
            }
        } else {
            if (! Auth::attempt(['email' => $loginInput, 'password' => $password], $remember)) {
                return back()->withInput($request->only('login', 'remember'))
                    ->withErrors(['login' => 'Email / Nomor WhatsApp atau Password salah.']);
            }
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'Akun Anda dinonaktifkan oleh Administrator Madrasah.']);
        }

        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole($user): RedirectResponse
    {
        return match ($user->role) {
            UserRole::Admin => redirect()->intended(route('admin.dashboard')),
            UserRole::Teacher => redirect()->intended(route('teacher.dashboard')),
            UserRole::Parent => redirect()->intended(route('parent.dashboard')),
            default => redirect()->route('home'),
        };
    }
}
