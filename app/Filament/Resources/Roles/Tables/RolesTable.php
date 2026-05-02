<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Models\RoleFilter;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Role Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('filters_summary')
                    ->label('Tablo Filtreleri')
                    ->getStateUsing(function ($record) {
                        try {
                            $filters = RoleFilter::where('role_id', $record->id)
                                ->where('filter_type', '!=', 'none')
                                ->get();

                            if ($filters->isEmpty()) {
                                return '—';
                            }

                            return $filters->map(function ($f) {
                                $label = RoleFilter::FILTER_TYPES[$f->filter_type] ?? $f->filter_type;
                                return "{$f->resource}: {$label}";
                            })->join(', ');
                        } catch (\Exception $e) {
                            return '—';
                        }
                    })
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
