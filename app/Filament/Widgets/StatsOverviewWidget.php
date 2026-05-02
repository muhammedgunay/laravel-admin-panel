<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        return [
            Stat::make('Toplam Kullanıcı', $totalUsers)
                ->description('Sistemdeki tüm kayıtlı hesaplar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Görsel olarak bir chart çizgisi ekler (dummy data)

            Stat::make('Aktif Kullanıcılar', $activeUsers)
                ->description('Giriş izni olan aktif kullanıcılar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pasif Kullanıcılar', $inactiveUsers)
                ->description('Hesabı devre dışı olanlar')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
