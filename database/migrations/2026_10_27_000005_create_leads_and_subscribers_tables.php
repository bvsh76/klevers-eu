<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 254)->index();
            $table->string('phone', 50)->nullable();
            $table->text('message')->nullable();
            $table->string('locale', 5);
            $table->string('source', 100)->nullable(); // откуда пришла заявка: форма/страница
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('new')->index();
            $table->timestamp('consent_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('utm')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 254)->unique();
            $table->string('locale', 5);
            $table->string('source', 100)->nullable(); // лид-магнит или форма
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestamp('consent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('leads');
    }
};
