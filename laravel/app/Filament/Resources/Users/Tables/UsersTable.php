<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('Created By'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updater.name')
                    ->label(__('Updated By'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \App\Traits\HasAdvancedFilters::getAdvancedFilter(\App\Models\User::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
                Action::make('impersonate')
                    ->label(__('Impersonate'))
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('Impersonate'))
                    ->modalDescription(fn ($record) => "'{$record->name}' kullanıcısı olarak giriş yapılacak. Devam etmek istiyor musunuz?")
                    ->modalSubmitActionLabel(__('Impersonate'))
                    ->visible(fn ($record) =>
                        auth()->user()?->can('Impersonate:User')
                        && $record->id !== auth()->id()
                        && !$record->hasRole('super_admin')
                        && !session()->has('impersonator_id')
                    )
                    ->action(function ($record) {
                        $currentUser = auth()->user();

                        // Orijinal kullanıcı ID'sini session'a kaydet (login'den ÖNCE)
                        session()->put('impersonator_id', $currentUser->id);
                        session()->put('impersonator_name', $currentUser->name);

                        // Hedef kullanıcıya geçiş
                        $guardName = Auth::getDefaultDriver(); // 'web'
                        Auth::guard($guardName)->loginUsingId($record->id);

                        // Filament'in AuthenticateSession hash'ini güncelle
                        // (bu olmazsa middleware session'ı geçersiz sayıp login'e atar)
                        session()->put(
                            'password_hash_' . $guardName,
                            $record->password
                        );

                        Notification::make()
                            ->title("{$record->name} olarak giriş yapıldı")
                            ->body("Orijinal hesabınıza dönmek için sayfanın üstündeki banner'ı kullanın.")
                            ->warning()
                            ->send();

                        return redirect(filament()->getUrl());
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label(__('Activate'))
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading(__('Activate'))
                        ->modalDescription('Seçili kullanıcıların hesapları aktifleştirilecek. Devam etmek istiyor musunuz?')
                        ->modalSubmitActionLabel(__('Activate'))
                        ->visible(fn () => auth()->user()?->can('ToggleActive:User'))
                        ->action(function (Collection $records): void {
                            $records->each(fn ($record) => $record->update(['is_active' => true]));
                            Notification::make()
                                ->title('Kullanıcılar aktifleştirildi')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('deactivate')
                        ->label(__('Deactivate'))
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('Deactivate'))
                        ->modalDescription('Seçili kullanıcıların hesaplarına erişim engellenecek. Devam etmek istiyor musunuz?')
                        ->modalSubmitActionLabel(__('Deactivate'))
                        ->visible(fn () => auth()->user()?->can('ToggleActive:User'))
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $superAdminSkipped = 0;
                            $records->each(function ($record) use (&$superAdminSkipped) {
                                if ($record->hasRole('super_admin') || $record->id === auth()->id()) {
                                    $superAdminSkipped++;
                                    return;
                                }
                                $record->update(['is_active' => false]);
                            });
                            $message = 'Kullanıcılar pasife alındı.';
                            if ($superAdminSkipped > 0) {
                                $message .= " ({$superAdminSkipped} kullanıcı atlandı: super_admin veya kendiniz)";
                            }
                            Notification::make()
                                ->title($message)
                                ->warning()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}