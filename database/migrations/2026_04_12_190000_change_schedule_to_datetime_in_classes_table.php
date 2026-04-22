<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();

        // Normalize any existing string schedules before converting the column type.
        if ($driver === 'mysql') {
            DB::statement("\n            UPDATE classes\n            SET schedule = CASE\n                WHEN schedule REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN CONCAT(schedule, ' 00:00:00')\n                WHEN schedule REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$' THEN CONCAT(schedule, ':00')\n                WHEN schedule REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$' THEN schedule\n                ELSE NULL\n            END\n        ");

            DB::statement("ALTER TABLE classes MODIFY schedule DATETIME NULL");
        } else {
            // PostgreSQL or others: use regex operator (~) and concat operator (||)
            DB::statement("\n            UPDATE classes\n            SET schedule = CASE\n                WHEN schedule ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN schedule || ' 00:00:00'\n                WHEN schedule ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$' THEN schedule || ':00'\n                WHEN schedule ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$' THEN schedule\n                ELSE NULL\n            END\n        ");

            // Try to alter column type to timestamp in a portable way
            DB::statement("ALTER TABLE classes ALTER COLUMN schedule TYPE timestamp USING schedule::timestamp");
        }
    }

    public function down(): void
    {
        $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE classes MODIFY schedule VARCHAR(255) NULL");
        } else {
            DB::statement("ALTER TABLE classes ALTER COLUMN schedule TYPE varchar(255)");
        }
    }
};
