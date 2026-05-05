<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Notification Center page permissions for Shield
        Permission::firstOrCreate(
            ['name' => 'page_SendNotification', 'guard_name' => 'web']
        );
        Permission::firstOrCreate(
            ['name' => 'page_NotificationHistory', 'guard_name' => 'web']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'page_SendNotification',
            'page_NotificationHistory',
        ])->where('guard_name', 'web')->delete();
    }
};
