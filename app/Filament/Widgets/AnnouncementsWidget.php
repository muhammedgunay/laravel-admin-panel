<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AnnouncementsWidget extends Widget
{
    protected string $view = 'filament.widgets.announcements-widget';

    protected static ?int $sort = -1; // Dashboard'da en üstte göster

    protected int|string|array $columnSpan = 'full';

    public function getAnnouncements(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return Announcement::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) use ($user) {
                $query->where('target_audience', 'all')
                      ->orWhere(function ($q) use ($user) {
                          $q->where('target_audience', 'department')
                            ->when($user->department_id, fn($sq) => $sq->where('department_id', $user->department_id), fn($sq) => $sq->whereRaw('1=0'));
                      });
                
                if ($user->hasRole('super_admin')) {
                    $query->orWhere('target_audience', 'admins');
                }
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('priority')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
    }

    public static function getTypeColor(string $type): string
    {
        return match ($type) {
            'warning' => '#f59e0b',
            'success' => '#10b981',
            'danger'  => '#ef4444',
            default   => '#3b82f6', // info
        };
    }

    public static function getTypeBg(string $type): string
    {
        return match ($type) {
            'warning' => 'bg-amber-50 border-amber-400 dark:bg-amber-950 dark:border-amber-500',
            'success' => 'bg-emerald-50 border-emerald-400 dark:bg-emerald-950 dark:border-emerald-500',
            'danger'  => 'bg-red-50 border-red-400 dark:bg-red-950 dark:border-red-500',
            default   => 'bg-blue-50 border-blue-400 dark:bg-blue-950 dark:border-blue-500',
        };
    }

    public static function getTypeTextColor(string $type): string
    {
        return match ($type) {
            'warning' => 'text-amber-800 dark:text-amber-200',
            'success' => 'text-emerald-800 dark:text-emerald-200',
            'danger'  => 'text-red-800 dark:text-red-200',
            default   => 'text-blue-800 dark:text-blue-200',
        };
    }

    public static function getTypeIcon(string $type): string
    {
        return match ($type) {
            'warning' => '⚠️',
            'success' => '✅',
            'danger'  => '🚨',
            default   => 'ℹ️',
        };
    }

    public static function getPriorityLabel(int $priority): string
    {
        return match ($priority) {
            5 => 'Critical',
            4 => 'Urgent',
            3 => 'High',
            2 => 'Normal',
            default => 'Low',
        };
    }
}
