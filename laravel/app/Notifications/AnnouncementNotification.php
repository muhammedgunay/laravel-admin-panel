<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title($this->announcement->title)
            ->body($this->announcement->description)
            ->status($this->announcement->type ?? 'info')
            ->icon($this->getIcon())
            ->getDatabaseMessage();
    }

    private function getIcon(): string
    {
        return match ($this->announcement->type) {
            'warning' => 'heroicon-o-exclamation-triangle',
            'success' => 'heroicon-o-check-circle',
            'danger'  => 'heroicon-o-x-circle',
            default   => 'heroicon-o-information-circle',
        };
    }
}
