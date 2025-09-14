<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('permission')->nullable();
            $table->json('roles')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->string('is_active', 10)->default('aktif');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
            $table->index(['parent_id', 'order']);
            $table->index('is_active'); // Untuk filter menu aktif/inaktif
            $table->index('name'); // Untuk search by name
            $table->index('url'); // Untuk search by URL
            $table->index('permission'); // Untuk filter by permission
            $table->index('roles'); // Untuk filter by roles
            $table->index(['is_active', 'parent_id', 'order']); // Composite index untuk sidebar query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
