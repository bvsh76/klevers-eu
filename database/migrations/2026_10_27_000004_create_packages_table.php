<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key', 50)->nullable()->unique();
            $table->jsonb('title');
            $table->jsonb('description')->nullable();
            $table->jsonb('features')->nullable();
            $table->decimal('price', 10, 2)->nullable(); // EUR; null = цена по запросу
            $table->string('price_period', 10)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(false)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
