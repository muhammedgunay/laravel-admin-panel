<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * @deprecated Artık kullanılmıyor — renderHook ile değiştirildi.
 * Silmek yerine gizliyoruz çünkü discoverWidgets bu dosyayı buluyor.
 */
class ImpersonationBanner extends Widget
{
    protected string $view = 'filament.widgets.impersonation-banner';

    protected static ?int $sort = -100;

    protected int | string | array $columnSpan = 'full';

    // Her zaman gizli — renderHook üstlendi
    public static function canView(): bool
    {
        return false;
    }

    protected function getViewData(): array
    {
        return [
            'impersonatorName' => session('impersonator_name', 'Bilinmeyen'),
            'currentUserName'  => auth()->user()?->name ?? 'Bilinmeyen',
        ];
    }
}
