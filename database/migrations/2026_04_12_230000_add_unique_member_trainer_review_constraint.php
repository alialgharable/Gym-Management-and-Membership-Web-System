<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the newest row when duplicates exist, then enforce uniqueness.
        DB::statement("\n            DELETE r1 FROM trainer_reviews r1\n            INNER JOIN trainer_reviews r2\n                ON r1.member_id = r2.member_id\n               AND r1.trainer_id = r2.trainer_id\n               AND r1.id < r2.id\n        ");

        DB::statement("ALTER TABLE trainer_reviews ADD UNIQUE KEY trainer_reviews_member_trainer_unique (member_id, trainer_id)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE trainer_reviews DROP INDEX trainer_reviews_member_trainer_unique");
    }
};
