<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('rooms')->insert([
            [
                'name' => 'Combat Sports Room',
                'description' => 'For boxing, MMA, kickboxing, and martial arts classes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yoga & Pilates Studio',
                'description' => 'Quiet studio for yoga, pilates, mobility, and stretching sessions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Group Training Room',
                'description' => 'General-purpose room for group classes and functional training.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fitness Machines Hall',
                'description' => 'Main floor with fitness machines and strength equipment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable()->after('trainer_id');
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("\n                UPDATE classes\n                SET room_id = CASE\n                    WHEN LOWER(name) REGEXP 'boxing|combat|mma|martial|kickboxing' THEN 1\n                    WHEN LOWER(name) REGEXP 'yoga|pilates' THEN 2\n                    WHEN LOWER(name) REGEXP 'crossfit|hiit|fitness|workout' THEN 4\n                    ELSE 3\n                END\n                WHERE room_id IS NULL\n            ");

            DB::statement('ALTER TABLE classes MODIFY room_id BIGINT UNSIGNED NOT NULL');
        } else {
            // PostgreSQL and other drivers: use POSIX regex (~*) for case-insensitive match
            DB::statement("\n                UPDATE classes\n                SET room_id = CASE\n                    WHEN LOWER(name) ~* 'boxing|combat|mma|martial|kickboxing' THEN 1\n                    WHEN LOWER(name) ~* 'yoga|pilates' THEN 2\n                    WHEN LOWER(name) ~* 'crossfit|hiit|fitness|workout' THEN 4\n                    ELSE 3\n                END\n                WHERE room_id IS NULL\n            ");

            // Alter column to bigint and set NOT NULL in a Postgres-compatible way
            try {
                DB::statement('ALTER TABLE classes ALTER COLUMN room_id TYPE bigint USING room_id::bigint');
                DB::statement('ALTER TABLE classes ALTER COLUMN room_id SET NOT NULL');
            } catch (\Throwable $e) {
                // If altering fails (e.g., column already correct), continue silently
            }
        }

        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->restrictOnDelete();
            $table->index(['room_id', 'schedule']);
            $table->index(['trainer_id', 'schedule']);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropIndex(['room_id', 'schedule']);
            $table->dropIndex(['trainer_id', 'schedule']);
            $table->dropColumn('room_id');
        });

        Schema::dropIfExists('rooms');
    }
};
