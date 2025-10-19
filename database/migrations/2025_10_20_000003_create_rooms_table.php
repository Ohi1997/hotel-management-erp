<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('room_type_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('floor_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('rate', 10, 2)->default(0);
            $table->string('status', 32)->default('available')->index();
            $table->string('clean_status', 32)->default('clean');
            $table->boolean('is_smoking')->default(false);
            $table->json('amenities')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
