<?php

namespace App\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

class NotificationHistory extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static \UnitEnum|string|null $navigationGroup = 'Notification Center';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.notification-history';

    public static function getNavigationLabel(): string
    {
        return __('Notification History');
    }

    public function getTitle(): string
    {
        return __('Notification History');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = DatabaseNotification::query()
            ->whereNull('read_at')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DatabaseNotification::query()
                    ->latest()
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->id)
                    ->sortable(),
                TextColumn::make('data.title')
                    ->label(__('Title'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('data->title', 'like', "%{$search}%");
                    })
                    ->limit(50),
                TextColumn::make('data.body')
                    ->label(__('Content'))
                    ->limit(40)
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
                TextColumn::make('notifiable_id')
                    ->label(__('Recipient'))
                    ->formatStateUsing(function ($record) {
                        $user = \App\Models\User::find($record->notifiable_id);
                        return $user?->name ?? __('Unknown');
                    })
                    ->searchable(),
                IconColumn::make('read_at')
                    ->label(__('Read'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->read_at !== null)
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedClock)
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Sent At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('read_status')
                    ->label(__('Read Status'))
                    ->options([
                        'read'   => __('Read'),
                        'unread' => __('Unread'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'read'   => $query->whereNotNull('read_at'),
                            'unread' => $query->whereNull('read_at'),
                            default  => $query,
                        };
                    }),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label(__('Mark as Read'))
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->visible(fn ($record) => $record->read_at === null)
                    ->action(function ($record) {
                        $record->markAsRead();
                        Notification::make()
                            ->title(__('Marked as read'))
                            ->success()
                            ->send();
                    }),
                Action::make('markAsUnread')
                    ->label(__('Mark as Unread'))
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->color('warning')
                    ->visible(fn ($record) => $record->read_at !== null)
                    ->action(function ($record) {
                        $record->update(['read_at' => null]);
                        Notification::make()
                            ->title(__('Marked as unread'))
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->label(__('Delete'))
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->delete();
                        Notification::make()
                            ->title(__('Notification deleted'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAllRead')
                        ->label(__('Mark as Read'))
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->markAsRead());
                            Notification::make()
                                ->title(__('Marked as read'))
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('deleteSelected')
                        ->label(__('Delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->delete());
                            Notification::make()
                                ->title(__('Notifications deleted'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('No notifications'))
            ->emptyStateDescription(__('No notifications have been sent yet.'))
            ->emptyStateIcon(Heroicon::OutlinedBellSlash);
    }
}
