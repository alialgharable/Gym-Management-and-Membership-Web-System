<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("\n                UPDATE trainers\n                SET specialty = CASE\n                    WHEN LOWER(specialty) REGEXP 'boxing|combat|mma|martial|kickboxing' THEN 'combat'\n                    WHEN LOWER(specialty) REGEXP 'yoga|pilates' THEN 'yoga_pilates'\n                    WHEN LOWER(specialty) REGEXP 'crossfit|functional|group|cardio' THEN 'group_training'\n                    WHEN LOWER(specialty) REGEXP 'fitness|strength|machine|workout' THEN 'fitness_machines'\n                    ELSE ELT(FLOOR(1 + RAND() * 4), 'combat', 'yoga_pilates', 'group_training', 'fitness_machines')\n                END\n            ");
        } else {
            // PostgreSQL: use ~* for case-insensitive regex and array/random selection
            DB::statement("\n                UPDATE trainers\n                SET specialty = CASE\n                    WHEN LOWER(specialty) ~* 'boxing|combat|mma|martial|kickboxing' THEN 'combat'\n                    WHEN LOWER(specialty) ~* 'yoga|pilates' THEN 'yoga_pilates'\n                    WHEN LOWER(specialty) ~* 'crossfit|functional|group|cardio' THEN 'group_training'\n                    WHEN LOWER(specialty) ~* 'fitness|strength|machine|workout' THEN 'fitness_machines'\n                    ELSE (ARRAY['combat','yoga_pilates','group_training','fitness_machines'])[floor(random()*4 + 1)::int]\n                END\n            ");
        }
    }

    public function down(): void
    {
        // No-op: cannot safely restore prior free-text values.
    }
};
