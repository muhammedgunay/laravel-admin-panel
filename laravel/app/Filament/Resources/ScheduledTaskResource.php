<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduledTaskResource\Pages;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTask;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ScheduledTaskResource extends Resource
{
    protected static ?string $model = MonitoredScheduledTask::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?string $modelLabel = 'Zamanlanmış Görev';

    protected static ?string $pluralModelLabel = 'Görev Zamanlayıcı';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Görev Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cron_expression')
                    ->label('Zaman (Cron)')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_started_at')
                    ->label('Son Başlama')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_finished_at')
                    ->label('Son Bitiş')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_failed_at')
                    ->label('Son Hata')
                    ->dateTime('d M Y H:i:s')
                    ->color('danger')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Read-only
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduledTasks::route('/'),
        ];
    }
}
