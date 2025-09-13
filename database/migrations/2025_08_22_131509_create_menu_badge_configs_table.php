<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_badge_configs', function (Blueprint $table) {
            $table->id();
            $table->string('menu_url')->unique();
            $table->string('model_class');
            $table->string('date_field')->default('created_at');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_badge_configs');
    }
};
