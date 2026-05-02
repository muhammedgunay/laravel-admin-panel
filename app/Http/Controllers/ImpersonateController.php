<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Taklit modundan çık, orijinal kullanıcıya geri dön.
     */
    public function leave(Request $request)
    {
        $impersonatorId = session()->get('impersonator_id');

        if (!$impersonatorId) {
            abort(403, 'Aktif bir taklit oturumu bulunamadı.');
        }

        $originalUser = User::findOrFail($impersonatorId);

        // Session'ı temizle
        session()->forget('impersonator_id');
        session()->forget('impersonator_name');

        // Orijinal kullanıcıya geri giriş yap
        $guardName = Auth::getDefaultDriver(); // 'web'
        Auth::guard($guardName)->loginUsingId($originalUser->id);

        // Filament AuthenticateSession hash'ini güncelle
        session()->put(
            'password_hash_' . $guardName,
            $originalUser->password
        );

        \Filament\Notifications\Notification::make()
            ->title('Orijinal hesabınıza geri döndünüz')
            ->body("{$originalUser->name} olarak devam ediyorsunuz.")
            ->success()
            ->send();

        return redirect(filament()->getUrl());
    }
}

