<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name', fn ($query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a department'),
                Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Placeholder::make('creator_name')
                    ->label('Created By')
                    ->content(fn ($record) => $record?->creator?->name ?? '—')
                    ->visibleOn('edit'),
                Placeholder::make('updater_name')
                    ->label('Updated By')
                    ->content(fn ($record) => $record?->updater?->name ?? '—')
                    ->visibleOn('edit'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Pasif kullanıcılar sisteme giriş yapamaz.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true)
                    ->visibleOn('edit')
                    ->disabled(fn () => !auth()->user()?->can('ToggleActive:User')),
            ]);
    }
}