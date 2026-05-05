<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\RoleFilter;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * Rol oluşturulduktan sonra filtre tercihlerini role_filters tablosuna kaydet.
     */
    protected function afterCreate(): void
    {
        $this->saveResourceFilters($this->record->id);
    }

    protected function saveResourceFilters(int $roleId): void
    {
        $resourceFilters = $this->data['resourceFilters'] ?? [];

        // Önce mevcut filtreleri temizle
        RoleFilter::where('role_id', $roleId)->delete();

        // Yeni filtreleri kaydet
        foreach ($resourceFilters as $item) {
            if (empty($item['resource'])) continue;

            RoleFilter::updateOrCreate(
                [
                    'role_id'  => $roleId,
                    'resource' => $item['resource'],
                ],
                [
                    'filter_type' => $item['filter_type'] ?? 'none',
                ]
            );
        }
    }
}
