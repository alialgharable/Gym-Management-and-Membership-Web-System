<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
        });

        DB::statement("\n            UPDATE classes\n            SET category = CASE\n                WHEN room_id = 1 THEN 'combat'\n                WHEN room_id = 2 THEN 'yoga_pilates'\n                WHEN room_id = 3 THEN 'group_training'\n                WHEN room_id = 4 THEN 'fitness_machines'\n                ELSE 'group_training'\n            END\n            WHERE category IS NULL\n        ");

        $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE classes MODIFY category VARCHAR(255) NOT NULL");
        } else {
            DB::statement("ALTER TABLE classes ALTER COLUMN category TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE classes ALTER COLUMN category SET NOT NULL");
        }

        Schema::table('classes', function (Blueprint $table) {
            $table->index('category', 'classes_category_index');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex('classes_category_index');
            $table->dropColumn('category');
        });
    }
};
