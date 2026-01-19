<?php

declare(strict_types=1);

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
        Schema::table('race_results', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('age_group_position');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('race_results', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['is_approved', 'approved_at', 'approved_by']);
        });
    }
};
