<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1);  // No FK
            $table->text('message');
            $table->json('meta')->nullable();
            $table->timestamps();
            // ✅ NO FOREIGN KEY CONSTRAINT!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
