<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

/**
 * Login dengan Google — hanya untuk akun yang SUDAH terdaftar.
 *
 * Pendaftaran mandiri ditutup: akun klien dibuat oleh tim dari Admin Panel.
 * Karena itu email Google yang belum dikenal ditolak, bukan dibuatkan akun baru —
 * kalau tidak, siapa pun dengan akun Google bisa masuk ke Client Panel.
 */
class GoogleAuthController extends Controller
{
    /**
     * Saat dijalankan di localhost / ngrok, pakai callback host yang sedang dibuka
     * supaya satu GOOGLE_REDIRECT_URI tidak memaksa semua orang ke domain produksi.
     */
    protected function configureRedirectUri(): void
    {
        $host = request()->getHost();

        if (in_array($host, ['127.0.0.1', 'localhost']) || str_contains($host, 'ngrok')) {
            config(['services.google.redirect' => route('auth.google.callback')]);
        }
    }

    public function redirect(): RedirectResponse
    {
        if (empty(config('services.google.client_id'))) {
            return redirect()->route('login')->with('error', 'Login dengan Google belum dikonfigurasi.');
        }

        $this->configureRedirectUri();

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Login dengan Google dibatalkan.');
        }

        $this->configureRedirectUri();

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback gagal: ' . $e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal terhubung ke Google. Silakan coba lagi.');
        }

        $email = strtolower((string) $googleUser->getEmail());
        $googleId = (string) $googleUser->getId();

        if ($email === '') {
            return redirect()->route('login')->with('error', 'Akun Google Anda tidak memiliki alamat email yang valid.');
        }

        $user = User::where('google_id', $googleId)->orWhere('email', $email)->first();

        // Akun belum terdaftar → tolak (tidak ada pendaftaran otomatis)
        if (! $user) {
            return redirect()->route('login')->with('error', "Akun Google {$email} belum terdaftar di VexaHost Client. Hubungi tim VexaHost via WhatsApp untuk dibuatkan akun.");
        }

        $updates = [];
        if (! $user->google_id) {
            $updates['google_id'] = $googleId;
        }
        if (! $user->avatar && $googleUser->getAvatar()) {
            $updates['avatar'] = $googleUser->getAvatar();
        }
        if ($updates) {
            $user->forceFill($updates)->save();
        }

        // Google sudah memverifikasi email-nya
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended($user->homeUrl());
    }
}
