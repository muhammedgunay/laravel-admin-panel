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
        // Impersonate:User permission oluştur (guard: web)
        Permission::firstOrCreate(
            ['name' => 'Impersonate:User', 'guard_name' => 'web']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'Impersonate:User')
                  ->where('guard_name', 'web')
                  ->delete();
    }
};
