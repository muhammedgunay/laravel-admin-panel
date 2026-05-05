<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $type = 'info',
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $notification = \Filament\Notifications\Notification::make()
            ->title($this->title)
            ->body($this->body)
            ->status($this->type)
            ->icon($this->getIcon());

        if ($this->actionUrl) {
            $notification->actions([
                \Filament\Actions\Action::make('view')
                    ->label($this->actionLabel ?? __('View'))
                    ->url($this->actionUrl)
                    ->button()
                    ->markAsRead(),
            ]);
        }

        return $notification->getDatabaseMessage();
    }

    private function getIcon(): string
    {
        return match ($this->type) {
            'warning' => 'heroicon-o-exclamation-triangle',
            'success' => 'heroicon-o-check-circle',
            'danger'  => 'heroicon-o-x-circle',
            default   => 'heroicon-o-information-circle',
        };
    }
}
