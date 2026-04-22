<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the newest row when duplicates exist, then enforce uniqueness.
        $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("\n                DELETE r1 FROM trainer_reviews r1\n                INNER JOIN trainer_reviews r2\n                    ON r1.member_id = r2.member_id\n                   AND r1.trainer_id = r2.trainer_id\n                   AND r1.id < r2.id\n            ");

            DB::statement("ALTER TABLE trainer_reviews ADD UNIQUE KEY trainer_reviews_member_trainer_unique (member_id, trainer_id)");
        } else {
            // PostgreSQL: delete duplicates via subquery
            DB::statement("DELETE FROM trainer_reviews WHERE id IN (SELECT r1.id FROM trainer_reviews r1 JOIN trainer_reviews r2 ON r1.member_id = r2.member_id AND r1.trainer_id = r2.trainer_id AND r1.id < r2.id)");

            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS trainer_reviews_member_trainer_unique ON trainer_reviews (member_id, trainer_id)");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE trainer_reviews DROP INDEX trainer_reviews_member_trainer_unique");
    }
};
