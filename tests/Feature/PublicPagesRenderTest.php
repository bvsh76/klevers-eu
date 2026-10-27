<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Segment;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    private function service(): Service
    {
        return Service::create([
            'key' => 'tax',
            'slug' => ['ru' => 'nalogi'],
            'title' => ['ru' => 'Налоги'],
            'intro' => ['ru' => 'Кратко о налогах'],
            'is_published' => true,
        ]);
    }

    private function segment(): Segment
    {
        return Segment::create([
            'key' => 'starters',
            'slug' => ['ru' => 'nachinayushchim'],
            'title' => ['ru' => 'Начинающим'],
            'is_published' => true,
        ]);
    }

    private function package(Service $service, string $key, string $title, ?int $price, bool $featured = false, bool $active = true): Package
    {
        return Package::create([
            'service_id' => $service->id,
            'key' => $key,
            'title' => ['ru' => $title],
            'price' => $price,
            'price_period' => 'month',
            'is_featured' => $featured,
            'is_active' => $active,
        ]);
    }

    public function test_home_shows_services_segments_and_only_featured_packages(): void
    {
        $service = $this->service();
        $this->segment();
        $this->package($service, 'biz', 'Бизнес', 1200, featured: true);
        $this->package($service, 'start', 'Стартовый', 49);

        $this->get('/ru')
            ->assertOk()
            ->assertSee('Налоги')
            ->assertSee('Кратко о налогах')
            ->assertSee('Начинающим')
            ->assertSee('Бизнес')
            ->assertSee("1\u{00A0}200\u{00A0}€")
            ->assertDontSee('Стартовый');
    }

    public function test_service_page_shows_active_packages_and_related_segments(): void
    {
        $service = $this->service();
        $service->segments()->attach($this->segment());
        $this->package($service, 'ind', 'Индивидуальный', null);
        $this->package($service, 'old', 'Архивный', 10, active: false);

        $this->get('/ru/uslugi/nalogi')
            ->assertOk()
            ->assertSee('Пакеты и цены')
            ->assertSee('Индивидуальный')
            ->assertSee('Цена по запросу')
            ->assertDontSee('Архивный')
            ->assertSee('Кому подходит')
            ->assertSee('Начинающим');
    }

    public function test_segment_page_shows_related_services(): void
    {
        $segment = $this->segment();
        $segment->services()->attach($this->service());

        $this->get('/ru/klientam/nachinayushchim')
            ->assertOk()
            ->assertSee('Подходящие услуги')
            ->assertSee('Налоги');
    }

    public function test_not_found_page_has_home_link(): void
    {
        $this->get('/ru/net-takoj-stranicy')
            ->assertNotFound()
            ->assertSee('404')
            ->assertSee(route('home', ['locale' => 'ru']), false);
    }
}
