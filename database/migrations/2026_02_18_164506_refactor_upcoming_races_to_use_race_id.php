<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('upcoming_races', function (Blueprint $table) {
            // Add race_id column with foreign key only if it doesn't exist
            if (!Schema::hasColumn('upcoming_races', 'race_id')) {
                $table->foreignId('race_id')
                    ->constrained('races')
                    ->cascadeOnDelete();
            }
        });

        Schema::table('upcoming_races', function (Blueprint $table) {
            // Add unique index for user_profile_id and race_id combination
            $table->unique(['user_profile_id', 'race_id']);
        });

        // Remove old columns in a separate schema operation
        Schema::table('upcoming_races', function (Blueprint $table) {
            $columns_to_drop = [];

            if (Schema::hasColumn('upcoming_races', 'race_type')) {
                $columns_to_drop[] = 'race_type';
            }
            if (Schema::hasColumn('upcoming_races', 'location')) {
                $columns_to_drop[] = 'location';
            }
            if (Schema::hasColumn('upcoming_races', 'race_date')) {
                $columns_to_drop[] = 'race_date';
            }

            if (!empty($columns_to_drop)) {
                $table->dropColumn($columns_to_drop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First drop the unique index
        Schema::table('upcoming_races', function (Blueprint $table) {
            $table->dropUnique(['user_profile_id', 'race_id']);
        });

        // Then drop the foreign key
        Schema::table('upcoming_races', function (Blueprint $table) {
            $table->dropForeign(['race_id']);
            $table->dropColumn('race_id');
        });

        // Restore the old columns
        Schema::table('upcoming_races', function (Blueprint $table) {
            $table->string('race_type');
            $table->string('location');
            $table->date('race_date');
        });
    }
};
