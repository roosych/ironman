<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path'); // relative path in storage
            $table->string('filename');
            $table->boolean('is_avatar')->default(false);
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_avatar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_photos');
    }
};
