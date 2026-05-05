<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->colors([
                        'info'    => 'site',
                        'warning' => 'mail',
                        'danger'  => 'security',
                        'gray'    => 'ui',
                    ])
                    ->sortable()
                    ->searchable(),

                TextColumn::make('label')
                    ->label('Setting')
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->description),

                TextColumn::make('key')
                    ->label('Key')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->limit(40)
                    ->placeholder('—')
                    ->tooltip(fn ($record) => $record->value),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('is_locked')
                    ->label('Locked')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Group')
                    ->options([
                        'site'     => 'Site',
                        'mail'     => 'Mail',
                        'security' => 'Security',
                        'ui'       => 'UI',
                    ]),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'string'  => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'float'   => 'Float',
                        'json'    => 'JSON',
                        'text'    => 'Text',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->disabled(fn ($record) => $record->is_locked),
            ])
            ->defaultSort('group')
            ->striped();
    }
}
