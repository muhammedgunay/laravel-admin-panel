<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Label')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('key')
                    ->label('Key')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Key değeri değiştirilemez.'),

                Select::make('group')
                    ->label('Group')
                    ->disabled()
                    ->dehydrated(false)
                    ->options([
                        'site'     => 'Site',
                        'mail'     => 'Mail',
                        'security' => 'Security',
                        'ui'       => 'UI',
                    ]),

                Select::make('type')
                    ->label('Type')
                    ->disabled()
                    ->dehydrated(false)
                    ->options([
                        'string'  => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'float'   => 'Float',
                        'json'    => 'JSON',
                        'text'    => 'Text',
                    ]),

                Textarea::make('description')
                    ->label('Description')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(2)
                    ->columnSpanFull(),

                // ── Editable value based on type ──────────────────

                TextInput::make('value')
                    ->label('Value')
                    ->nullable()
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && ! in_array($record->type, ['boolean', 'text', 'json']) && ! $record->is_locked),

                Textarea::make('value')
                    ->label('Value')
                    ->nullable()
                    ->rows(4)
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && $record->type === 'text' && ! $record->is_locked),

                Textarea::make('value')
                    ->label('Value (JSON)')
                    ->nullable()
                    ->rows(6)
                    ->columnSpanFull()
                    ->helperText('Geçerli JSON formatında giriniz.')
                    ->visible(fn ($record) => $record && $record->type === 'json' && ! $record->is_locked),

                Toggle::make('value')
                    ->label('Value')
                    ->columnSpanFull()
                    ->afterStateHydrated(fn ($component, $state) => $component->state(filter_var($state, FILTER_VALIDATE_BOOLEAN)))
                    ->dehydrateStateUsing(fn ($state) => $state ? 'true' : 'false')
                    ->visible(fn ($record) => $record && $record->type === 'boolean' && ! $record->is_locked),

                Placeholder::make('locked_notice')
                    ->label('')
                    ->content('🔒 Bu ayar kilitlidir ve panel üzerinden değiştirilemez.')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record?->is_locked),

                Placeholder::make('updater_name')
                    ->label('Last Updated By')
                    ->content(fn ($record) => $record?->updater?->name ?? '—')
                    ->visibleOn('edit'),
            ]);
    }
}
