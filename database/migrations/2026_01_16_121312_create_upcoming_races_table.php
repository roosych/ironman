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
        Schema::create('upcoming_races', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('race_type');
            $table->string('location');
            $table->date('race_date');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upcoming_races');
    }
};
