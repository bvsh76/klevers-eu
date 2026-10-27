<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoTest extends TestCase
{
    public function test_canonical_and_hreflang_ignore_query_string(): void
    {
        $response = $this->get('/en?utm_source=x');

        $response->assertSee('<link rel="canonical" href="'.route('home', ['locale' => 'en']).'">', false);

        foreach (array_keys(config('locales.supported')) as $locale) {
            $response->assertSee('hreflang="'.$locale.'" href="'.route('home', ['locale' => $locale]).'">', false);
        }
    }

    public function test_robots_disallows_everything_by_default(): void
    {
        config(['seo.indexable' => false]);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /', false);
    }

    public function test_robots_allows_indexing_and_points_to_sitemap_when_indexable(): void
    {
        config(['seo.indexable' => true]);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('sitemap.xml'), false)
            ->assertDontSee('Disallow: /', false);
    }

    public function test_sitemap_lists_every_locale_with_alternates(): void
    {
        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee('hreflang="x-default"', false);

        foreach (array_keys(config('locales.supported')) as $locale) {
            $response->assertSee('<loc>'.route('home', ['locale' => $locale]).'</loc>', false);
        }
    }

    public function test_not_found_page_is_localized(): void
    {
        $this->get('/en/nope')
            ->assertNotFound()
            ->assertSee('<html lang="en">', false)
            ->assertSee('Page not found');

        $this->get('/de/nope')
            ->assertNotFound()
            ->assertSee('<html lang="'.config('locales.default').'">', false);
    }
}
