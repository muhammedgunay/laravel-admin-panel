<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\RoleFilter;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Sayfa açılırken mevcut filtreleri Repeater'a yükle.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['resourceFilters'] = RoleFilter::where('role_id', $this->record->id)
            ->get()
            ->map(fn ($f) => [
                'resource'    => $f->resource,
                'filter_type' => $f->filter_type,
            ])
            ->toArray();

        return $data;
    }

    /**
     * Kaydet butonuna basınca filtreleri güncelle.
     */
    protected function afterSave(): void
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
