<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_athlete_id')->constrained('user_profiles')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('source_athlete_id');
            $table->index('status');
            $table->index('reviewed_by');

            // Partial unique indexes будут созданы отдельно для pending статуса
            // MySQL не поддерживает partial indexes, используем уникальные составные индексы
            // Бизнес-логика будет проверять уникальность pending запросов в коде
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_transfer_requests');
    }
};
