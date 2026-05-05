<?php

namespace App\Filament\Pages;

use App\Models\Department;
use App\Models\User;
use App\Notifications\SystemNotification;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Role;

class SendNotification extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static \UnitEnum|string|null $navigationGroup = 'Notification Center';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.send-notification';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'info',
            'target' => 'all',
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('Send Notification');
    }

    public function getTitle(): string
    {
        return __('Send Notification');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('notificationTitle')
                    ->label(__('Title'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('Notification title...')),
                RichEditor::make('notificationBody')
                    ->label(__('Content'))
                    ->required()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'h2', 'h3', 'bulletList', 'orderedList',
                        'link', 'blockquote', 'redo', 'undo',
                    ])
                    ->columnSpanFull(),
                Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'info'    => __('Info'),
                        'warning' => __('Warning'),
                        'success' => __('Success'),
                        'danger'  => __('Danger'),
                    ])
                    ->required(),
                Select::make('target')
                    ->label(__('Target Audience'))
                    ->options([
                        'all'        => __('All Users'),
                        'roles'      => __('By Role'),
                        'department' => __('By Department'),
                        'users'      => __('Specific Users'),
                    ])
                    ->required()
                    ->live(),
                Select::make('selectedRoles')
                    ->label(__('Roles'))
                    ->multiple()
                    ->options(fn () => Role::pluck('name', 'name')->toArray())
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('target') === 'roles')
                    ->required(fn ($get) => $get('target') === 'roles'),
                Select::make('selectedDepartment')
                    ->label(__('Department'))
                    ->options(fn () => Department::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('target') === 'department')
                    ->required(fn ($get) => $get('target') === 'department'),
                Select::make('selectedUsers')
                    ->label(__('Users'))
                    ->multiple()
                    ->options(fn () => User::where('is_active', true)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('target') === 'users')
                    ->required(fn ($get) => $get('target') === 'users'),
                TextInput::make('actionUrl')
                    ->label(__('Action URL'))
                    ->url()
                    ->placeholder('https://...')
                    ->helperText(__('Optional. Users can click to navigate.')),
                TextInput::make('actionLabel')
                    ->label(__('Action Label'))
                    ->maxLength(50)
                    ->placeholder(__('View'))
                    ->helperText(__('Button text for the action URL.')),
            ]);
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $recipients = $this->resolveRecipients($data);

        if ($recipients->isEmpty()) {
            Notification::make()
                ->title(__('No recipients found'))
                ->body(__('No users match the selected criteria.'))
                ->warning()
                ->send();
            return;
        }

        $notification = new SystemNotification(
            title: $data['notificationTitle'],
            body: strip_tags($data['notificationBody']),
            type: $data['type'],
            actionUrl: $data['actionUrl'] ?? null,
            actionLabel: $data['actionLabel'] ?? null,
        );

        $count = 0;
        foreach ($recipients as $user) {
            $user->notify($notification);
            $count++;
        }

        Notification::make()
            ->title(__('Notification sent successfully'))
            ->body(__(':count user(s) notified.', ['count' => $count]))
            ->success()
            ->send();

        $this->form->fill([
            'type' => 'info',
            'target' => 'all',
        ]);
    }

    private function resolveRecipients(array $data)
    {
        return match ($data['target']) {
            'roles'      => User::where('is_active', true)
                                ->role($data['selectedRoles'] ?? [])
                                ->get(),
            'department' => User::where('is_active', true)
                                ->where('department_id', $data['selectedDepartment'] ?? null)
                                ->get(),
            'users'      => User::where('is_active', true)
                                ->whereIn('id', $data['selectedUsers'] ?? [])
                                ->get(),
            default      => User::where('is_active', true)->get(), // 'all'
        };
    }
}
