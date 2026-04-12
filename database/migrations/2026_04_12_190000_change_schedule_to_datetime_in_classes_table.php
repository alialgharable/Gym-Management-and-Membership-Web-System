<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize any existing string schedules before converting the column type.
        DB::statement("\n            UPDATE classes\n            SET schedule = CASE\n                WHEN schedule REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN CONCAT(schedule, ' 00:00:00')\n                WHEN schedule REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$' THEN CONCAT(schedule, ':00')\n                WHEN schedule REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$' THEN schedule\n                ELSE NULL\n            END\n        ");

        DB::statement("ALTER TABLE classes MODIFY schedule DATETIME NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE classes MODIFY schedule VARCHAR(255) NULL");
    }
};
