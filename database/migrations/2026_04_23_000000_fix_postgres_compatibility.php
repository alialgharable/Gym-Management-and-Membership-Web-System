<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'pgsql') {
            // Nothing to do for non-Postgres drivers here.
            return;
        }

        // 1) Normalize `classes.schedule` to timestamp if possible
        try {
            DB::statement("\n                UPDATE classes\n                SET schedule = CASE\n                    WHEN schedule ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' THEN schedule || ' 00:00:00'\n                    WHEN schedule ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$' THEN schedule || ':00'\n                    WHEN schedule ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$' THEN schedule\n                    ELSE NULL\n                END\n            ");

            DB::statement("ALTER TABLE classes ALTER COLUMN schedule TYPE timestamp USING schedule::timestamp");
        } catch (\Throwable $e) {
            // ignore conversion failures; leave column as-is if conversion fails
        }

        // 2) Ensure `category` exists, populate and index it
        if (!Schema::hasColumn('classes', 'category')) {
            Schema::table('classes', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('category')->nullable();
            });
        }

        DB::statement("\n            UPDATE classes\n            SET category = CASE\n                WHEN room_id = 1 THEN 'combat'\n                WHEN room_id = 2 THEN 'yoga_pilates'\n                WHEN room_id = 3 THEN 'group_training'\n                WHEN room_id = 4 THEN 'fitness_machines'\n                ELSE 'group_training'\n            END\n            WHERE category IS NULL\n        ");

        try {
            DB::statement("ALTER TABLE classes ALTER COLUMN category TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE classes ALTER COLUMN category SET NOT NULL");
        } catch (\Throwable $e) {
            // ignore
        }

        if (!Schema::hasColumn('classes', 'category')) {
            // already handled above, safety
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('classes', 'category')) {
            // nothing
        }

        // Create index if not exists
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        try {
            $indexes = array_map(function ($i) { return $i->getName(); }, $sm->listTableIndexes('classes'));
        } catch (\Throwable $e) {
            $indexes = [];
        }

        if (!in_array('classes_category_index', $indexes, true)) {
            Schema::table('classes', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->index('category', 'classes_category_index');
            });
        }

        // 3) Populate room_id for classes using PHP regex matching to avoid MySQL REGEXP
        if (Schema::hasColumn('classes', 'name') && Schema::hasColumn('classes', 'room_id')) {
            $classes = DB::table('classes')->select('id', 'name')->get();
            foreach ($classes as $row) {
                $name = strtolower((string) $row->name);
                $roomId = 3; // default
                if (preg_match('/boxing|combat|mma|martial|kickboxing/', $name)) {
                    $roomId = 1;
                } elseif (preg_match('/yoga|pilates/', $name)) {
                    $roomId = 2;
                } elseif (preg_match('/crossfit|hiit|fitness|workout/', $name)) {
                    $roomId = 4;
                }

                DB::table('classes')->where('id', $row->id)->update(['room_id' => $roomId]);
            }

            // Try to set NOT NULL for room_id
            try {
                DB::statement("ALTER TABLE classes ALTER COLUMN room_id SET NOT NULL");
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 4) Deduplicate trainer_reviews and add unique constraint (member_id, trainer_id)
        if (Schema::hasTable('trainer_reviews')) {
            // keep highest id for each member/trainer pair
            $rows = DB::table('trainer_reviews')
                ->select(DB::raw('member_id, trainer_id, MAX(id) as keep_id'))
                ->groupBy('member_id', 'trainer_id')
                ->get()
                ->pluck('keep_id')
                ->toArray();

            if (!empty($rows)) {
                DB::table('trainer_reviews')->whereNotIn('id', $rows)->delete();
            }

            // add unique index if not exists
            try {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = array_map(function ($i) { return $i->getName(); }, $sm->listTableIndexes('trainer_reviews'));
            } catch (\Throwable $e) {
                $indexes = [];
            }

            if (!in_array('trainer_reviews_member_trainer_unique', $indexes, true)) {
                Schema::table('trainer_reviews', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->unique(['member_id', 'trainer_id'], 'trainer_reviews_member_trainer_unique');
                });
            }
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return;
        }

        // We won't attempt to fully reverse complex data transformations.
    }
};
