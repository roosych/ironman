<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['athlete', 'coach', 'admin'])->default('athlete');
            $table->string('ironman_number')->nullable();
            $table->string('bio')->nullable();
            $table->json('social_links')->nullable(); // {strava, instagram, facebook}
            $table->timestamps();

            $table->unique('user_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
