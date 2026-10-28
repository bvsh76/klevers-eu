<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    private function contactsPage(): void
    {
        $page = new Page;
        $page->key = 'contacts';
        $page->is_published = true;
        $page->setTranslations('slug', ['ru' => 'kontakty', 'et' => 'kontakt', 'en' => 'contacts']);
        $page->setTranslations('title', ['ru' => 'Контакты', 'et' => 'Kontakt', 'en' => 'Contacts']);
        $page->save();
    }

    public function test_banner_is_rendered_and_ga_meta_only_when_configured(): void
    {
        $this->get('/ru')
            ->assertOk()
            ->assertSee('x-data="consent"', false)
            ->assertSee('consent-open', false)
            ->assertDontSee('name="ga4-id"', false)
            ->assertDontSee('googletagmanager', false);

        Setting::put('ga4_id', 'G-ABC1234');

        $this->get('/ru')
            ->assertSee('<meta name="ga4-id" content="G-ABC1234">', false)
            ->assertDontSee('googletagmanager', false);
    }

    public function test_banner_is_on_not_found_page(): void
    {
        $this->get('/de')->assertNotFound()->assertSee('x-data="consent"', false);
    }

    public function test_booking_block_hidden_without_embed(): void
    {
        $this->contactsPage();

        $this->get('/ru/kontakty')->assertOk()->assertDontSee('id="booking"', false);
    }

    public function test_booking_embed_waits_for_consent(): void
    {
        $this->contactsPage();
        Setting::put('booking_embed', '<div class="calendly-inline-widget"></div><script src="https://assets.calendly.com/assets/external/widget.js"></script>');

        $html = $this->get('/ru/kontakty')->assertOk()->assertSee('id="booking"', false)->getContent();

        $this->assertMatchesRegularExpression('#<template data-consent="external"><div class="calendly-inline-widget"></div><script src="https://assets\.calendly\.com/assets/external/widget\.js"></script></template>#', $html);
        $this->assertStringContainsString('data-consent-placeholder', $html);
        $this->assertStringContainsString('consent-grant-external', $html);
        $this->assertSame(1, substr_count($html, 'assets.calendly.com'));
    }
}
