<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Models\Department;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatik oluşturulur, değiştirilebilir.'),
                Textarea::make('description')
                    ->label('Short Description')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Kısa özet...'),
                RichEditor::make('content')
                    ->label('Content')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'h2', 'h3', 'bulletList', 'orderedList',
                        'link', 'blockquote', 'redo', 'undo',
                    ])
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'info'    => 'Info',
                        'warning' => 'Warning',
                        'success' => 'Success',
                        'danger'  => 'Danger',
                    ])
                    ->default('info')
                    ->required(),
                Select::make('priority')
                    ->label('Priority')
                    ->options([
                        1 => '1 - Low',
                        2 => '2 - Normal',
                        3 => '3 - High',
                        4 => '4 - Urgent',
                        5 => '5 - Critical',
                    ])
                    ->default(1)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                Select::make('target_audience')
                    ->label('Target Audience')
                    ->options([
                        'all'        => 'All Users',
                        'admins'     => 'Admins Only',
                        'department' => 'Specific Department',
                    ])
                    ->default('all')
                    ->required()
                    ->live(),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a department')
                    ->visible(fn ($get) => $get('target_audience') === 'department'),
                DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->nullable()
                    ->placeholder('Immediately'),
                DateTimePicker::make('expires_at')
                    ->label('Expiry Date')
                    ->nullable()
                    ->placeholder('Never'),
                Toggle::make('is_pinned')
                    ->label('Pin this announcement')
                    ->default(false),
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
