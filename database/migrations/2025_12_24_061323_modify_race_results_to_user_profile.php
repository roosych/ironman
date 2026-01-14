<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Change race_results to belong to user_profile instead of user.
     */
    public function up(): void
    {
        Schema::table('race_results', function (Blueprint $table) {
            // Drop existing user_id foreign key and index
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'race_date']);
        });

        Schema::table('race_results', function (Blueprint $table) {
            // Remove user_id column
            $table->dropColumn('user_id');

            // Add user_profile_id
            $table->foreignId('user_profile_id')
                ->after('id')
                ->constrained('user_profiles')
                ->cascadeOnDelete();

            // Re-add index with user_profile_id
            $table->index(['user_profile_id', 'race_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('race_results', function (Blueprint $table) {
            $table->dropForeign(['user_profile_id']);
            $table->dropIndex(['user_profile_id', 'race_date']);
            $table->dropColumn('user_profile_id');
        });

        Schema::table('race_results', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->index(['user_id', 'race_date']);
        });
    }
};
