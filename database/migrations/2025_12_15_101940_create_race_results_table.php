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
        Schema::create('race_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('race_date');
            $table->string('location');
            $table->enum('race_type', ['ironman', 'ironman_70_3', '5150']);
            $table->unsignedInteger('swim_time');
            $table->unsignedInteger('t1_time');
            $table->unsignedInteger('bike_time');
            $table->unsignedInteger('t2_time');
            $table->unsignedInteger('run_time');
            $table->unsignedInteger('total_time');
            $table->string('age_group')->nullable();
            $table->unsignedInteger('overall_position')->nullable();
            $table->unsignedInteger('age_group_position')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'race_date']);
            $table->index('race_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_results');
    }
};
