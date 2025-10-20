<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('group');
            $table->json('values');
            $table->timestamps();
            $table->unique(['hotel_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_settings');
    }
};
