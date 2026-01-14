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
     * Make user_id nullable and add admin_full_name for data-first architecture.
     * Profiles can now exist without a linked user.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Re-add foreign key with nullOnDelete
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // Add unique constraint (allows multiple NULLs in MySQL)
            $table->unique('user_id');

            // Add admin_full_name for profiles without linked users
            $table->string('admin_full_name')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('admin_full_name');

            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique('user_id');
        });
    }
};
