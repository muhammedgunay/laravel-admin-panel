<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Department Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter department name'),
                Select::make('manager_id')
                    ->label('Manager')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a manager'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Placeholder::make('creator_name')
                    ->label('Created By')
                    ->content(fn ($record) => $record?->creator?->name ?? '—')
                    ->visibleOn('edit'),
                Placeholder::make('updater_name')
                    ->label('Updated By')
                    ->content(fn ($record) => $record?->updater?->name ?? '—')
                    ->visibleOn('edit'),
            ]);
    }
}
