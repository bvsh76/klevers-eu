<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComingSoonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['launch.coming_soon_hosts' => ['klevers.eu']]);
    }

    public function test_listed_host_gets_coming_soon_page_in_url_locale(): void
    {
        $this->get('http://klevers.eu/en')
            ->assertOk()
            ->assertSee('Our website is being updated')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_old_urls_on_listed_host_get_coming_soon_page_in_default_locale(): void
    {
        $this->withHeader('Accept-Language', 'de-DE,de;q=0.9')
            ->get('http://klevers.eu/old-page.html')
            ->assertOk()
            ->assertSee('Сайт обновляется');
    }

    public function test_root_uses_browser_language(): void
    {
        $this->withHeader('Accept-Language', 'et-EE,et;q=0.9')
            ->get('http://klevers.eu/')
            ->assertOk()
            ->assertSee('Veebileht on uuendamisel');
    }

    public function test_admin_and_robots_stay_available(): void
    {
        $this->get('http://klevers.eu/admin/login')
            ->assertOk()
            ->assertDontSee('Сайт обновляется');

        $this->get('http://klevers.eu/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /');
    }

    public function test_messenger_buttons_follow_settings(): void
    {
        $this->get('http://klevers.eu/ru')->assertDontSee('wa.me', false);

        Setting::put('whatsapp', '37255555555');
        Setting::put('telegram', 'klevers_test');

        $this->get('http://klevers.eu/ru')
            ->assertSee('https://wa.me/37255555555', false)
            ->assertSee('https://t.me/klevers_test', false);
    }

    public function test_other_hosts_get_the_site(): void
    {
        $this->get('http://other.test/')
            ->assertRedirect('http://other.test/ru');
    }
}
