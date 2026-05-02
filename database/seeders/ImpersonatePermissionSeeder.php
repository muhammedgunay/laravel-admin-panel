<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ImpersonatePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permission'ı oluştur (yoksa)
        $permission = Permission::firstOrCreate(
            ['name' => 'Impersonate:User', 'guard_name' => 'web']
        );

        // super_admin rolüne bu yetkiyi ver
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin && !$superAdmin->hasPermissionTo('Impersonate:User')) {
            $superAdmin->givePermissionTo($permission);
        }

        $this->command->info('Impersonate:User yetkisi oluşturuldu ve super_admin rolüne atandı.');
        $this->command->info('Diğer rollere yetki vermek için Admin panelinde Roles > Edit > Permissions kısmını kullanın.');
    }
}
