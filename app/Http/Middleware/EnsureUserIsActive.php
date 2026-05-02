<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Oturum açmış kullanıcı pasife alındıysa oturumu sonlandırır.
     * Login engeli CustomLogin'de yapılır; bu middleware mevcut oturumları yakalar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()?->is_active) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->to(filament()->getCurrentPanel()?->getLoginUrl() ?? '/admin/login')
                ->with('error', 'Hesabınız devre dışı bırakılmıştır. Lütfen yöneticinizle iletişime geçin.');
        }

        return $next($request);
    }
}
