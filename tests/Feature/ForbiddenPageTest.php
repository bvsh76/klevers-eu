<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ForbiddenPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_forbidden_page_is_localized_and_links_home(): void
    {
        Route::get('/en/forbidden_test', fn () => abort(403));

        $this->get('/en/forbidden_test')
            ->assertForbidden()
            ->assertSee('Access denied')
            ->assertSee('/en', false);
    }
}
