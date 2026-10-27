<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Снимок языков на момент миграции; новый язык = новая миграция индекса. */
    private array $locales = ['ru', 'et', 'en'];

    public function up(): void
    {
        foreach (['segments', 'services'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('key', 50)->nullable()->unique();
                $table->jsonb('slug');
                $table->jsonb('title');
                $table->jsonb('intro')->nullable();
                $table->jsonb('body')->nullable();
                $table->jsonb('meta_title')->nullable();
                $table->jsonb('meta_description')->nullable();
                $table->boolean('is_published')->default(false)->index();
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();
            });

            foreach ($this->locales as $locale) {
                DB::statement("CREATE UNIQUE INDEX {$name}_slug_{$locale}_unique ON {$name} ((slug->>'{$locale}'))");
            }
        }

        Schema::create('segment_service', function (Blueprint $table) {
            $table->foreignId('segment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->primary(['segment_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segment_service');
        Schema::dropIfExists('services');
        Schema::dropIfExists('segments');
    }
};
