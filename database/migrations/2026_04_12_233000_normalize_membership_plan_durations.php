<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize all durations to logical day buckets: 30, 90, 180, 365.
        DB::statement("\n            UPDATE membership_plans\n            SET duration = CASE\n                WHEN duration <= 45 THEN 30\n                WHEN duration <= 135 THEN 90\n                WHEN duration <= 270 THEN 180\n                ELSE 365\n            END\n        ");
    }

    public function down(): void
    {
        // No-op: previous arbitrary values cannot be restored reliably.
    }
};
