<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Mail\ConfirmSubscription;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function subscribe(array $data = [], string $locale = 'ru')
    {
        return $this->withoutDefer()->from("/{$locale}")->post(route('subscribe', ['locale' => $locale]), array_merge([
            'sub_email' => 'Anna@Example.com',
            'sub_consent' => '1',
        ], $data));
    }

    private function confirmUrl(Subscriber $subscriber): string
    {
        return URL::temporarySignedRoute('subscribe.confirm', now()->addDays(7), ['locale' => $subscriber->locale, 'subscriber' => $subscriber->id]);
    }

    public function test_footer_has_subscribe_form(): void
    {
        $this->get('/ru')->assertOk()->assertSee('name="sub_email"', false)->assertSee('id="subscribe"', false);
    }

    public function test_new_subscriber_gets_confirmation_mail(): void
    {
        Mail::fake();

        $this->subscribe([], 'et')->assertRedirect(url('/et').'#subscribe')->assertSessionHas('subscribe_sent', true);

        $subscriber = Subscriber::sole();
        $this->assertSame('anna@example.com', $subscriber->email);
        $this->assertSame('et', $subscriber->locale);
        $this->assertNotNull($subscriber->consent_at);
        $this->assertNull($subscriber->confirmed_at);
        Mail::assertSent(ConfirmSubscription::class, fn (ConfirmSubscription $mail) => $mail->hasTo('anna@example.com') && $mail->locale === 'et');
    }

    public function test_validation_and_honeypot(): void
    {
        Mail::fake();

        $this->subscribe(['sub_email' => 'nope', 'sub_consent' => null])->assertSessionHasErrors(['sub_email', 'sub_consent']);
        $this->subscribe(['sub_website' => 'spam'])->assertSessionHas('subscribe_sent', true);

        $this->assertSame(0, Subscriber::count());
        Mail::assertNothingSent();
    }

    public function test_confirmed_subscriber_is_not_touched(): void
    {
        Mail::fake();
        $subscriber = Subscriber::create(['email' => 'anna@example.com', 'locale' => 'ru', 'confirmed_at' => now()->subDay()]);

        $this->subscribe()->assertSessionHas('subscribe_sent', true);

        $this->assertTrue($subscriber->fresh()->confirmed_at->isYesterday());
        Mail::assertNothingSent();
    }

    public function test_unsubscribed_can_subscribe_again(): void
    {
        Mail::fake();
        Subscriber::create(['email' => 'anna@example.com', 'locale' => 'ru', 'confirmed_at' => now(), 'unsubscribed_at' => now()]);

        $this->subscribe();

        $subscriber = Subscriber::sole();
        $this->assertNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->unsubscribed_at);
        Mail::assertSent(ConfirmSubscription::class);
    }

    public function test_signed_link_confirms_and_shows_lead_magnet(): void
    {
        Setting::put('lead_magnet_url', 'https://example.com/checklist.pdf');
        $subscriber = Subscriber::create(['email' => 'anna@example.com', 'locale' => 'ru']);

        $this->get($this->confirmUrl($subscriber))
            ->assertOk()
            ->assertSee(__('site.subscribe_confirmed_title', [], 'ru'))
            ->assertSee('https://example.com/checklist.pdf', false);

        $this->assertNotNull($subscriber->fresh()->confirmed_at);
    }

    public function test_tampered_link_is_rejected(): void
    {
        $subscriber = Subscriber::create(['email' => 'anna@example.com', 'locale' => 'ru']);

        $this->get($this->confirmUrl($subscriber).'0')->assertForbidden();
        $this->get(route('subscribe.confirm', ['locale' => 'ru', 'subscriber' => $subscriber->id]))->assertForbidden();

        $this->assertNull($subscriber->fresh()->confirmed_at);
    }

    public function test_unsubscribe_flow(): void
    {
        $subscriber = Subscriber::create(['email' => 'anna@example.com', 'locale' => 'ru', 'confirmed_at' => now()]);
        $url = route('unsubscribe', ['locale' => 'ru', 'token' => $subscriber->unsubscribe_token]);

        $this->get($url)->assertOk()->assertSee(__('site.unsubscribe_button', [], 'ru'));
        $this->assertNull($subscriber->fresh()->unsubscribed_at);

        $this->post(route('unsubscribe.store', ['locale' => 'ru', 'token' => $subscriber->unsubscribe_token]))
            ->assertOk()
            ->assertSee(__('site.unsubscribed_title', [], 'ru'));
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);

        $this->get(route('unsubscribe', ['locale' => 'ru', 'token' => str_repeat('a', 48)]))->assertNotFound();
    }

    public function test_confirmation_mail_is_localized(): void
    {
        $subscriber = Subscriber::create(['email' => 'anna@example.com', 'locale' => 'et']);

        $mail = (new ConfirmSubscription($subscriber))->locale('et');
        $html = $mail->render();

        $this->assertStringContainsString(__('site.confirm_mail_button', [], 'et'), $html);
        $this->assertStringContainsString('/et/subscribe/confirm/'.$subscriber->id, $html);
        $this->assertStringContainsString('/et/unsubscribe/'.$subscriber->unsubscribe_token, $html);
    }

    public function test_admin_lead_magnet_url_must_be_https(): void
    {
        $admin = User::factory()->create();
        $admin->role = Role::Admin;
        $admin->save();

        $this->actingAs($admin)->put('/admin/settings', ['lead_magnet_url' => 'http://example.com/a.pdf'])
            ->assertSessionHasErrors('lead_magnet_url');
        $this->actingAs($admin)->put('/admin/settings', ['lead_magnet_url' => 'https://example.com/a.pdf'])
            ->assertSessionHasNoErrors();

        $this->assertSame('https://example.com/a.pdf', Setting::get('lead_magnet_url'));
    }
}
