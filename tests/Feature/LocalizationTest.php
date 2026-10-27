<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')
            ->assertRedirect(route('home', ['locale' => config('locales.default')]));
    }

    public function test_supported_locales_render_with_html_lang(): void
    {
        foreach (array_keys(config('locales.supported')) as $locale) {
            $this->get("/{$locale}")
                ->assertOk()
                ->assertSee('<html lang="'.$locale.'">', false);
        }
    }

    public function test_unsupported_locale_returns_404(): void
    {
        $this->get('/de')->assertNotFound();
    }

    public function test_page_has_hreflang_for_all_locales_and_x_default(): void
    {
        $response = $this->get('/et');

        foreach (array_keys(config('locales.supported')) as $locale) {
            $response->assertSee('hreflang="'.$locale.'" href="'.route('home', ['locale' => $locale]).'"', false);
        }

        $response->assertSee('hreflang="x-default" href="'.route('home', ['locale' => config('locales.default')]).'"', false);
    }

    public function test_switcher_marks_current_locale_and_links_others(): void
    {
        $this->get('/en')
            ->assertSee('aria-label="Language"', false)
            ->assertSee('aria-current="true" lang="en"', false)
            ->assertSee('<span class="sr-only">English</span>', false)
            ->assertSee('href="'.route('home', ['locale' => 'ru']).'" hreflang="ru"', false);
    }
}
