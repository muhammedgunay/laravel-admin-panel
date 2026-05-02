<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleFilter extends Model
{
    protected $fillable = [
        'role_id',
        'resource',
        'filter_type',
    ];

    /**
     * Kullanılabilir filtre tipleri
     */
    public const FILTER_TYPES = [
        'none'             => 'Filtre Yok (Tümünü Gör)',
        'own_only'         => 'Sadece Kendi Kayıtları',
        'department_only'  => 'Sadece Departman Kayıtları',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Belirtilen rol ve resource için filtre tipini döner.
     * Önce cache'e bakar, yoksa DB'den alır.
     */
    public static function getFilterType(int $roleId, string $resource): string
    {
        $filter = static::where('role_id', $roleId)
                        ->where('resource', $resource)
                        ->first();

        return $filter?->filter_type ?? 'none';
    }

    /**
     * Kullanıcının aktif rollerine sahip en kısıtlayıcı filtreyi döner.
     * Öncelik: own_only > department_only > none
     */
    public static function resolveForUser(User $user, string $resource): string
    {
        if ($user->hasRole('super_admin')) {
            return 'none';
        }

        $roleIds = $user->roles->pluck('id');

        $filters = static::whereIn('role_id', $roleIds)
                         ->where('resource', $resource)
                         ->pluck('filter_type')
                         ->unique()
                         ->toArray();

        if (empty($filters)) {
            return 'none';
        }

        // En az kısıtlayıcı semantik: birden fazla rol varsa
        // en geniş yetkiyi ver (none > department_only > own_only)
        if (in_array('none', $filters))            return 'none';
        if (in_array('department_only', $filters)) return 'department_only';
        return 'own_only';
    }
}
