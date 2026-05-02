<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_filters', function (Blueprint $table) {
            $table->id();

            // Spatie roles tablosuna foreign key
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');

            // Resource adı: 'Announcement', 'User', 'Department' vb.
            $table->string('resource');

            // Filtre tipi: 'none', 'own_only', 'department_only'
            $table->string('filter_type')->default('none');

            $table->timestamps();

            // Her rol için her resource'un sadece 1 filtresi olsun
            $table->unique(['role_id', 'resource']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_filters');
    }
};
