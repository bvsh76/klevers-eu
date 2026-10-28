<?php

namespace Tests\Feature;

use App\Mail\LeadReceived;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Иван',
            'email' => 'ivan@example.com',
            'message' => 'Нужна консультация',
            'consent' => '1',
        ], $override);
    }

    public function test_mail_is_sent_to_configured_address(): void
    {
        Mail::fake();
        Setting::put('lead_email', 'ops@example.com');

        $this->withoutDefer()->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload());

        Mail::assertSent(LeadReceived::class, fn (LeadReceived $mail) => $mail->hasTo('ops@example.com')
            && $mail->hasReplyTo('ivan@example.com')
            && $mail->lead->is(Lead::sole()));
    }

    public function test_no_mail_without_configured_address(): void
    {
        Mail::fake();

        $this->withoutDefer()->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload());

        Mail::assertNothingSent();
        $this->assertSame(1, Lead::count());
    }

    public function test_no_mail_for_honeypot(): void
    {
        Mail::fake();
        Setting::put('lead_email', 'ops@example.com');

        $this->withoutDefer()->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload(['website' => 'spam']));

        Mail::assertNothingSent();
    }

    public function test_mail_failure_does_not_break_form(): void
    {
        Setting::put('lead_email', 'ops@example.com');
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('smtp down'));

        $this->withoutDefer()->from('/ru')->post(route('lead.store', ['locale' => 'ru']), $this->payload())
            ->assertRedirect()
            ->assertSessionHas('lead_sent', true);

        $this->assertSame(1, Lead::count());
    }

    public function test_mail_renders_lead_details(): void
    {
        $lead = Lead::create($this->payload(['phone' => '+372 5555 5555', 'locale' => 'ru', 'source' => '/ru/kontakty']));

        $html = (new LeadReceived($lead))->render();

        $this->assertStringContainsString('Иван', $html);
        $this->assertStringContainsString('+372 5555 5555', $html);
        $this->assertStringContainsString('/ru/kontakty', $html);
        $this->assertStringContainsString(route('admin.leads.show', $lead->id), $html);
    }
}
