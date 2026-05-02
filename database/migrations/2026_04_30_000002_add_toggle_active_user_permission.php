<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'ToggleActive:User', 'guard_name' => 'web']
        );

        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin && !$superAdmin->hasPermissionTo('ToggleActive:User')) {
            $superAdmin->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        Permission::where('name', 'ToggleActive:User')->delete();
    }
};
