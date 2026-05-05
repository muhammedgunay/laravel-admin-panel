<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /**
     * Login sonrası is_active kontrolü yapar.
     * Pasif kullanıcıyı logout edip hata mesajı gösterir.
     */
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        // Başarılı login olduysa ama kullanıcı pasifse → geri at
        if ($response !== null && auth()->check() && !auth()->user()?->is_active) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            throw ValidationException::withMessages([
                'data.email' => 'Hesabınız devre dışı bırakılmıştır. Lütfen yöneticinizle iletişime geçin.',
            ]);
        }

        return $response;
    }
}
