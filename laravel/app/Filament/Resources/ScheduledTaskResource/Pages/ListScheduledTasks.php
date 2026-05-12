<?php

namespace App\Filament\Resources\ScheduledTaskResource\Pages;

use App\Filament\Resources\ScheduledTaskResource;
use Filament\Resources\Pages\ListRecords;

class ListScheduledTasks extends ListRecords
{
    protected static string $resource = ScheduledTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
