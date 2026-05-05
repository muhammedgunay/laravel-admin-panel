<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general')->comment('site, mail, security, ui, etc.');
            $table->string('key')->unique()->comment('Dot notation: site.name, mail.from_address');
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'boolean', 'integer', 'float', 'json', 'text'])->default('string')->comment('Used for casting');
            $table->string('label')->comment('Human readable label for panel display');
            $table->text('description')->nullable()->comment('Helper text shown in the panel');
            $table->boolean('is_public')->default(false)->comment('Can be exposed to frontend/API');
            $table->boolean('is_locked')->default(false)->comment('Prevents editing from panel UI');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
