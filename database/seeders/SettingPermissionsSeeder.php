<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingPermissionsSeeder extends Seeder
{
    /**
     * Setting tablosu için Spatie Permission kayıtlarını oluşturur
     * ve super_admin rolüne otomatik olarak atar.
     */
    public function run(): void
    {
        $permissions = [
            'ViewAny:Setting',
            'View:Setting',
            'Create:Setting',
            'Update:Setting',
            'Delete:Setting',
            'Restore:Setting',
            'ForceDelete:Setting',
            'ForceDeleteAny:Setting',
            'RestoreAny:Setting',
            'Replicate:Setting',
            'Reorder:Setting',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // super_admin rolüne tüm Setting yetkilerini ver
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            foreach ($permissions as $perm) {
                if (!$superAdmin->hasPermissionTo($perm)) {
                    $superAdmin->givePermissionTo($perm);
                }
            }
        }

        $this->command->info('Setting yetkileri oluşturuldu ve super_admin rolüne atandı.');
        $this->command->info('Diğer rollere yetki vermek için Admin panelinde Roles > Edit > Permissions kısmını kullanın.');
    }
}
