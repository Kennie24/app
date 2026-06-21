<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist');
            $table->string('slug')->unique();
            $table->decimal('price', 8, 2)->default(0);
            $table->unsignedInteger('redemption_limit')->default(0);
            $table->unsignedInteger('redemptions')->default(0);
            $table->enum('status', ['live', 'scheduled', 'archived'])->default('scheduled');
            $table->string('cover_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
