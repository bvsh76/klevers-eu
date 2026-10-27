<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentTest extends TestCase
{
    use RefreshDatabase;

    private function service(): Service
    {
        return Service::create([
            'slug' => ['ru' => 'buhgalteriya', 'en' => 'accounting'],
            'title' => ['ru' => 'Бухгалтерия', 'en' => 'Accounting'],
            'body' => ['ru' => '<p>Текст услуги</p>', 'en' => '<p>Service text</p>'],
            'is_published' => true,
        ]);
    }

    public function test_page_by_slug(): void
    {
        Page::create(['slug' => ['ru' => 'kontakty'], 'title' => ['ru' => 'Контакты'], 'body' => ['ru' => '<p>Адрес</p>'], 'is_published' => true]);
        Page::create(['slug' => ['ru' => 'chernovik'], 'title' => ['ru' => 'Черновик'], 'is_published' => false]);

        $this->get('/ru/kontakty')->assertOk()->assertSee('Контакты</h1>', false)->assertSee('<p>Адрес</p>', false);
        $this->get('/ru/chernovik')->assertNotFound();
        $this->get('/en/kontakty')->assertNotFound();
    }

    public function test_home_page_record_redirects_to_home(): void
    {
        Page::create(['key' => 'home', 'slug' => ['ru' => 'home'], 'title' => ['ru' => 'Главная'], 'is_published' => true]);

        $this->get('/ru/home')->assertStatus(301)->assertRedirect('/ru');
    }

    public function test_service_by_translated_section_and_slug(): void
    {
        $this->service();

        $this->get('/ru/uslugi/buhgalteriya')->assertOk()->assertSee('Текст услуги');
        $this->get('/en/services/accounting')->assertOk()->assertSee('Service text');
        $this->get('/en/uslugi/accounting')->assertNotFound();
        $this->get('/ru/uslugi/accounting')->assertNotFound();
        $this->get('/et/teenused/buhgalteriya')->assertNotFound();
    }

    public function test_hreflang_and_switcher_follow_translated_slugs(): void
    {
        $this->service();

        $html = $this->get('/ru/uslugi/buhgalteriya')->assertOk()->getContent();

        $this->assertStringContainsString('hreflang="en" href="'.url('/en/services/accounting').'"', $html);
        $this->assertStringContainsString('hreflang="x-default" href="'.url('/ru/uslugi/buhgalteriya').'"', $html);
        $this->assertStringNotContainsString('<link rel="alternate" hreflang="et"', $html);
        $this->assertStringContainsString('href="'.url('/en/services/accounting').'" hreflang="en"', $html);
        $this->assertStringContainsString('href="'.url('/et').'" hreflang="et"', $html);
    }

    public function test_sitemap_includes_published_content_with_alternates(): void
    {
        $this->service();
        Service::create(['slug' => ['ru' => 'skryto'], 'title' => ['ru' => 'Скрыто'], 'is_published' => false]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.url('/ru/uslugi/buhgalteriya').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.url('/en/services/accounting').'</loc>', $xml);
        $this->assertStringNotContainsString('/et/teenused/', $xml);
        $this->assertStringNotContainsString('skryto', $xml);
    }
}
