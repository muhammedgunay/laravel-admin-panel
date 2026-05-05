<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\RoleFilter;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    /**
     * Sistemdeki tüm Resource'lar — yeni Resource ekledikçe buraya da ekle.
     * Bu listede olmayan Resource'lar için filtre uygulanmaz.
     */
    public static function getResources(): array
    {
        return [
            'Announcement' => 'Announcements',
            'User'         => 'Users',
            'Department'   => 'Departments',
            'Setting'      => 'Settings',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Rol adı ───────────────────────────────────────────────
                TextInput::make('name')
                    ->label('Role Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                // ── Yetkiler ──────────────────────────────────────────────
                Select::make('permissions')
                    ->label('Permissions')
                    ->relationship('permissions', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                // ── Tablo Bazlı Filtreler ─────────────────────────────────
                // Her Resource için ayrı bir filtre tipi satırı.
                // Kaydedilen veriler role_filters tablosuna custom mantıkla yazılır.
                Repeater::make('resourceFilters')
                    ->label('Tablo Bazlı Veri Filtreleri')
                    ->helperText('Her tablo için kullanıcıların hangi kayıtları göreceğini, güncelleyeceğini ve sileceğini belirleyin.')
                    ->schema([
                        Select::make('resource')
                            ->label('Tablo')
                            ->options(static::getResources())
                            ->required()
                            ->distinct()
                            ->live(),

                        Select::make('filter_type')
                            ->label('Filtre Tipi')
                            ->options(RoleFilter::FILTER_TYPES)
                            ->required()
                            ->default('none')
                            ->helperText(fn ($get) => match ($get('filter_type')) {
                                'own_only'        => '🔒 Kullanıcı sadece kendi oluşturduğu kayıtları görebilir, düzenleyebilir ve silebilir.',
                                'department_only' => '🏢 Kullanıcı sadece kendi departmanının kayıtlarını görebilir, düzenleyebilir ve silebilir.',
                                default           => '🌍 Kısıtlama yok — yetkili olduğu tüm kayıtları görebilir.',
                            }),
                    ])
                    ->columns(2)
                    ->addActionLabel('Tablo Filtresi Ekle')
                    ->defaultItems(0)
                    ->reorderable(false)
                    // Dehydrate — veriler Repeater'dan role_filters tablosuna yazılır
                    ->dehydrated(false), // Manuel save kullanacağız (Pages\EditRole)
            ]);
    }
}
