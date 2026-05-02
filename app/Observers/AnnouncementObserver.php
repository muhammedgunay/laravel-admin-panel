<?php

namespace App\Observers;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;

class AnnouncementObserver
{
    /**
     * Duyuru published statüsüne geçtiğinde ilgili kullanıcılara bildirim gönder.
     */
    public function updated(Announcement $announcement): void
    {
        // Sadece status draft/archived → published geçişinde tetikle
        if (
            $announcement->isDirty('status') &&
            $announcement->status === 'published' &&
            $announcement->getOriginal('status') !== 'published'
        ) {
            $users = $this->resolveTargetUsers($announcement);

            foreach ($users as $user) {
                // Kendi kendine bildirim gönderme
                if ($user->id === $announcement->created_by) {
                    continue;
                }
                $user->notify(new AnnouncementNotification($announcement));
            }
        }
    }

    /**
     * target_audience'a göre hedef kullanıcıları belirle.
     */
    private function resolveTargetUsers(Announcement $announcement)
    {
        return match ($announcement->target_audience) {
            'admins'     => User::role('super_admin')->get(),
            'department' => User::where('department_id', $announcement->department_id)->get(),
            default      => User::all(), // 'all'
        };
    }
}
