<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    private function contactsPage(): Page
    {
        $page = new Page;
        $page->key = 'contacts';
        $page->is_published = true;
        $page->setTranslations('slug', ['ru' => 'kontakty', 'et' => 'kontakt', 'en' => 'contacts']);
        $page->setTranslations('title', ['ru' => 'Контакты', 'et' => 'Kontakt', 'en' => 'Contacts']);
        $page->save();

        return $page;
    }

    /** @return array<string, mixed> */
    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => '  Иван  ',
            'email' => 'Ivan@Example.COM',
            'phone' => '+372 5555 5555',
            'message' => 'Нужна консультация',
            'consent' => '1',
        ], $override);
    }

    public function test_contacts_page_shows_form(): void
    {
        $this->contactsPage();

        $this->get('/ru/kontakty')
            ->assertOk()
            ->assertSee('id="lead-form"', false)
            ->assertSee('name="consent"', false)
            ->assertSee('name="website"', false);
    }

    public function test_valid_lead_is_saved(): void
    {
        $this->from('/ru/kontakty')->post(route('lead.store', ['locale' => 'ru']), $this->payload())
            ->assertRedirect(url('/ru/kontakty').'#lead-form')
            ->assertSessionHas('lead_sent', true);

        $lead = Lead::sole();
        $this->assertSame('Иван', $lead->name);
        $this->assertSame('ivan@example.com', $lead->email);
        $this->assertSame('ru', $lead->locale);
        $this->assertSame('/ru/kontakty', $lead->source);
        $this->assertNotNull($lead->consent_at);
    }

    public function test_success_message_is_shown_after_redirect(): void
    {
        $this->contactsPage();

        $this->withSession(['lead_sent' => true])->get('/ru/kontakty')
            ->assertOk()
            ->assertSee(__('site.lead_sent_title', [], 'ru'))
            ->assertDontSee('name="consent"', false);
    }

    public function test_required_fields_and_consent(): void
    {
        $this->from('/ru/kontakty')->post(route('lead.store', ['locale' => 'ru']), [])
            ->assertSessionHasErrors(['name', 'email', 'consent']);

        $this->from('/ru/kontakty')->post(route('lead.store', ['locale' => 'ru']), $this->payload(['consent' => null, 'phone' => 'abc']))
            ->assertSessionHasErrors(['consent', 'phone']);

        $this->assertSame(0, Lead::count());
    }

    public function test_honeypot_pretends_success_without_saving(): void
    {
        $this->from('/ru/kontakty')->post(route('lead.store', ['locale' => 'ru']), $this->payload(['website' => 'http://spam.example']))
            ->assertRedirect(url('/ru/kontakty').'#lead-form')
            ->assertSessionHas('lead_sent', true);

        $this->assertSame(0, Lead::count());
    }

    public function test_utm_is_remembered_and_saved(): void
    {
        $this->get('/ru?utm_source=google&utm_campaign=autumn&foo=bar')
            ->assertSessionHas('utm', ['utm_source' => 'google', 'utm_campaign' => 'autumn']);

        $this->withSession(['utm' => ['utm_source' => 'google']])
            ->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload());

        $this->assertSame(['utm_source' => 'google'], Lead::sole()->utm);
    }

    public function test_lead_locale_follows_url(): void
    {
        $this->from('/et')->post(route('lead.store', ['locale' => 'et']), $this->payload());

        $this->assertSame('et', Lead::sole()->locale);
    }

    public function test_requests_are_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload())->assertRedirect();
        }

        $this->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload())->assertStatus(429);
        $this->assertSame(5, Lead::count());
    }
}
