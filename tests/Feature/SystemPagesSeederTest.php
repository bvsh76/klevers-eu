<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\SystemPagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemPagesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_system_pages_with_all_locales(): void
    {
        $this->seed(SystemPagesSeeder::class);

        $this->assertEqualsCanonicalizing(['home', 'about', 'contacts', 'privacy'], Page::pluck('key')->all());
        $privacy = Page::where('key', 'privacy')->sole();
        $this->assertSame('privacy-policy', $privacy->getTranslation('slug', 'en'));
        $this->assertTrue((bool) $privacy->is_published);
    }

    public function test_does_not_overwrite_existing_pages(): void
    {
        $this->seed(SystemPagesSeeder::class);
        $contacts = Page::where('key', 'contacts')->sole();
        $contacts->setTranslation('title', 'ru', 'Свяжитесь с нами');
        $contacts->save();

        $this->seed(SystemPagesSeeder::class);

        $this->assertSame(4, Page::count());
        $this->assertSame('Свяжитесь с нами', $contacts->fresh()->getTranslation('title', 'ru'));
    }

    public function test_forms_link_to_privacy_page(): void
    {
        $this->seed(SystemPagesSeeder::class);

        $this->get('/ru/kontakty')
            ->assertOk()
            ->assertSee('id="lead-form"', false)
            ->assertSee('href="'.url('/ru/politika-konfidencialnosti').'"', false);
    }
}
