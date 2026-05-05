<?php

namespace App\Filament\Resources;

use Spatie\Activitylog\Models\Activity;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Sistem Yönetimi';
    
    protected static ?string $modelLabel = 'İşlem Kaydı';
    
    protected static ?string $pluralModelLabel = 'İşlem Geçmişi';
    
    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('causer_id')
                    ->label('İşlemi Yapan')
                    ->formatStateUsing(fn ($record) => $record?->causer ? $record->causer->name : 'Sistem')
                    ->disabled(),
                TextInput::make('subject_type')
                    ->label('Etkilenen Tablo/Model')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => class_basename($state)),
                TextInput::make('description')
                    ->label('Yapılan İşlem')
                    ->disabled(),
                KeyValue::make('properties.attributes')
                    ->label('Yeni Değerler / Eklenenler')
                    ->disabled()
                    ->columnSpanFull(),
                KeyValue::make('properties.old')
                    ->label('Eski Değerler')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('causer_id')
                    ->label('Kullanıcı')
                    ->formatStateUsing(fn ($record) => $record->causer ? $record->causer->name : 'Sistem')
                    ->searchable(query: function ($query, $search) {
                        // MorphTo ilişki olduğu için özel arama yapıyoruz
                        $query->whereHasMorph('causer', [\App\Models\User::class], function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('description')
                    ->label('İşlem Tipi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'primary',
                    }),
                TextColumn::make('subject_type')
                    ->label('Etkilenen Model')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \App\Traits\HasAdvancedFilters::getAdvancedFilter(\Spatie\Activitylog\Models\Activity::class),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()->label('Detay'),
            ])
            ->bulkActions([]);
    }

    // Logların admin panelden manuel oluşturulmasını, düzenlenmesini veya silinmesini engelliyoruz.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
    
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ActivityResource\Pages\ListActivities::route('/'),
        ];
    }
}
