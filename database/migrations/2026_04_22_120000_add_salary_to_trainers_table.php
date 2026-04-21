<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->decimal('salary', 10, 2)->nullable()->after('profile_image');
        });

        $trainerIds = DB::table('trainers')->pluck('id');

        foreach ($trainerIds as $trainerId) {
            DB::table('trainers')
                ->where('id', $trainerId)
                ->update([
                    'salary' => random_int(1200, 3000),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn('salary');
        });
    }
};
