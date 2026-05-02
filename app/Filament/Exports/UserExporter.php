<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Ad Soyad'),
            ExportColumn::make('email')
                ->label('E-posta'),
            ExportColumn::make('department.name')
                ->label('Departman'),
            ExportColumn::make('roles.name')
                ->label('Roller'),
            ExportColumn::make('created_at')
                ->label('Kayıt Tarihi')
                ->formatStateUsing(fn ($state) => $state?->format('d.m.Y H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Kullanıcı dışa aktarma işlemi tamamlandı. Toplam ' . number_format($export->successful_rows) . ' kayıt dışa aktarıldı.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' kayıt dışa aktarılamadı.';
        }

        return $body;
    }
}
