<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessengersTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_links_without_settings(): void
    {
        $this->get('/ru')->assertOk()->assertDontSee('wa.me', false)->assertDontSee('t.me/', false);
    }

    public function test_links_in_header_and_footer(): void
    {
        Setting::put('whatsapp', '37255555555');
        Setting::put('telegram', 'klevers_test');

        $html = $this->get('/en')->assertOk()->getContent();

        $this->assertGreaterThanOrEqual(3, substr_count($html, 'href="https://wa.me/37255555555"'));
        $this->assertGreaterThanOrEqual(3, substr_count($html, 'href="https://t.me/klevers_test"'));
        $this->assertStringContainsString('aria-label="Message us on WhatsApp"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer"', $html);
    }

    public function test_contacts_page_has_labeled_buttons(): void
    {
        $page = new Page;
        $page->key = 'contacts';
        $page->is_published = true;
        $page->setTranslations('slug', ['ru' => 'kontakty', 'et' => 'kontakt', 'en' => 'contacts']);
        $page->setTranslations('title', ['ru' => 'Контакты', 'et' => 'Kontakt', 'en' => 'Contacts']);
        $page->save();
        Setting::put('telegram', 'klevers_test');

        $this->get('/ru/kontakty')->assertOk()->assertSee('Написать в Telegram');
        $this->get('/et/kontakt')->assertOk()->assertSee("Kirjuta Telegram'is");
    }
}
