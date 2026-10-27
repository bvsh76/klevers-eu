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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->nullable()->unique(); // системные страницы: home, about, contacts…
            $table->jsonb('slug');
            $table->jsonb('title');
            $table->jsonb('body')->nullable();
            $table->jsonb('meta_title')->nullable();
            $table->jsonb('meta_description')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        foreach ($this->locales as $locale) {
            DB::statement("CREATE UNIQUE INDEX pages_slug_{$locale}_unique ON pages ((slug->>'{$locale}'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
