<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExportPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Export:User permission oluştur
        $permission = Permission::firstOrCreate(
            ['name' => 'Export:User', 'guard_name' => 'web']
        );

        // Super Admin rolüne ver
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin && !$superAdmin->hasPermissionTo('Export:User')) {
            $superAdmin->givePermissionTo($permission);
        }

        $this->command->info('Export:User yetkisi oluşturuldu ve super_admin rolüne atandı.');
        $this->command->info('Diğer rollere (ör. departman müdürü) yetki vermek için Admin panelini kullanın.');
    }
}
