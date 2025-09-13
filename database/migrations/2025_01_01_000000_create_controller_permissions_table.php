<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controller_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('controller');
            $table->string('method');
            $table->string('permission');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['controller', 'method']);
            $table->index(['controller', 'method', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controller_permissions');
    }
};
