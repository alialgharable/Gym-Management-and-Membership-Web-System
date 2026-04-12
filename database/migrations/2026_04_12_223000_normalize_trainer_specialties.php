<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("\n            UPDATE trainers\n            SET specialty = CASE\n                WHEN LOWER(specialty) REGEXP 'boxing|combat|mma|martial|kickboxing' THEN 'combat'\n                WHEN LOWER(specialty) REGEXP 'yoga|pilates' THEN 'yoga_pilates'\n                WHEN LOWER(specialty) REGEXP 'crossfit|functional|group|cardio' THEN 'group_training'\n                WHEN LOWER(specialty) REGEXP 'fitness|strength|machine|workout' THEN 'fitness_machines'\n                ELSE ELT(FLOOR(1 + RAND() * 4), 'combat', 'yoga_pilates', 'group_training', 'fitness_machines')\n            END\n        ");
    }

    public function down(): void
    {
        // No-op: cannot safely restore prior free-text values.
    }
};
