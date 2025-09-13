<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('controller_permissions', function (Blueprint $table) {
            $table->json('permissions')->after('permission');
        });

        // Migrate existing data
        DB::table('controller_permissions')->get()->each(function ($row) {
            DB::table('controller_permissions')
                ->where('id', $row->id)
                ->update(['permissions' => json_encode([$row->permission])]);
        });

        Schema::table('controller_permissions', function (Blueprint $table) {
            $table->dropColumn('permission');
        });
    }

    public function down(): void
    {
        Schema::table('controller_permissions', function (Blueprint $table) {
            $table->string('permission')->after('method');
        });

        // Migrate back
        DB::table('controller_permissions')->get()->each(function ($row) {
            $permissions = json_decode($row->permissions, true);
            DB::table('controller_permissions')
                ->where('id', $row->id)
                ->update(['permission' => $permissions[0] ?? '']);
        });

        Schema::table('controller_permissions', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
