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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['privacy', 'terms', 'data_processing', 'cookies'])
                ->comment('Type of policy: privacy, terms, data_processing, cookies');
            $table->string('language', 2)
                ->comment('Language code (ISO 639-1): en, ru, az, etc.');
            $table->string('title', 255)
                ->comment('Policy title');
            $table->longText('content')
                ->comment('HTML content of the policy');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Whether this policy version is active');
            $table->timestamp('effective_date')
                ->useCurrent()
                ->comment('When this policy version becomes effective');
            $table->timestamps();

            // Ensure only one active policy per type and language
            $table->unique(['type', 'language', 'is_active'], 'unique_active_policy');

            // Index for faster lookups
            $table->index(['type', 'language', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
