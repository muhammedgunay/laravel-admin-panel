<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class LatestNotificationsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return __('My Recent Notifications');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DatabaseNotification::query()
                    ->where('notifiable_type', \App\Models\User::class)
                    ->where('notifiable_id', auth()->id())
                    ->latest()
            )
            ->columns([
                TextColumn::make('data.title')
                    ->label(__('Title'))
                    ->limit(50),
                TextColumn::make('data.body')
                    ->label(__('Content'))
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->data['body'] ?? '—'),
                TextColumn::make('data.status')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger'  => 'danger',
                        default   => 'info',
                    }),
                IconColumn::make('read_at')
                    ->label(__('Read'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->read_at !== null)
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedClock)
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('created_at')
                    ->label(__('Sent At'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('No notifications'))
            ->emptyStateDescription(__('You have no notifications yet.'))
            ->emptyStateIcon(Heroicon::OutlinedBellSlash);
    }
}
