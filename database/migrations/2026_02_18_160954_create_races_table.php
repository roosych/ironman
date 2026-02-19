<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('location');
            $table->string('type');
            $table->string('country_iso', 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index('date');
            $table->index('type');
            $table->index('country_iso');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
