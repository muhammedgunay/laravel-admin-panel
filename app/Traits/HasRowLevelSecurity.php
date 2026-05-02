<?php

namespace App\Traits;

use App\Models\RoleFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filament Resource'lara row-level security ekler.
 *
 * Kullanım:
 *   class AnnouncementResource extends Resource
 *   {
 *       use HasRowLevelSecurity;
 *   }
 *
 * Resource adını otomatik algılar (class_basename($model)).
 * Filtreyi role_filters tablosundan okur.
 */
trait HasRowLevelSecurity
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1=0');
        }

        // Super admin tüm kayıtları görür
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Resource adını model class'ından al (Announcement, User, Department...)
        $resource = class_basename(static::$model);

        // Kullanıcının rollerine göre filtre tipini hesapla
        $filterType = RoleFilter::resolveForUser($user, $resource);

        return match ($filterType) {
            'own_only'        => static::applyOwnOnlyFilter($query, $user),
            'department_only' => static::applyDepartmentFilter($query, $user),
            default           => $query, // 'none' → tümünü göster
        };
    }

    protected static function applyOwnOnlyFilter(Builder $query, $user): Builder
    {
        // created_by kolonu varsa filtrele
        $model = new static::$model;
        if (in_array('created_by', $model->getFillable())) {
            return $query->where('created_by', $user->id);
        }

        // Yoksa id üzerinden dene (users tablosu gibi)
        return $query->where('id', $user->id);
    }

    protected static function applyDepartmentFilter(Builder $query, $user): Builder
    {
        if (!$user->department_id) {
            // Departmanı yoksa kendi kaydına düşür
            return static::applyOwnOnlyFilter($query, $user);
        }

        $model = new static::$model;

        if (in_array('department_id', $model->getFillable())) {
            return $query->where('department_id', $user->department_id);
        }

        // department_id yoksa (örn: users) kullanıcıyı kendi kaydına düşür
        return static::applyOwnOnlyFilter($query, $user);
    }

    /**
     * Policy'lerin view/update/delete kontrolü için kullanılır.
     * Resource adına göre kullanıcının bu kaydı görmesi/düzenlemesi gerekip gerekmediğini kontrol eder.
     */
    public static function canAccessRecord($user, $record): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $resource   = class_basename(static::$model ?? get_class($record));
        $filterType = RoleFilter::resolveForUser($user, $resource);

        return match ($filterType) {
            'own_only'        => $record->created_by === $user->id,
            'department_only' => $record->department_id === $user->department_id,
            default           => true,
        };
    }
}
