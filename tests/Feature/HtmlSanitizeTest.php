<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HtmlSanitizeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_body_is_sanitized_on_output(): void
    {
        Page::create([
            'slug' => ['ru' => 'test'],
            'title' => ['ru' => 'Тест'],
            'body' => ['ru' => '<p>ok</p><script>alert(1)</script><img src=x onerror="alert(1)">'],
            'is_published' => true,
        ]);

        $this->get('/ru/test')
            ->assertOk()
            ->assertSee('<p>ok</p>', false)
            ->assertDontSee('<script>alert(1)', false)
            ->assertDontSee('onerror', false);
    }
}
