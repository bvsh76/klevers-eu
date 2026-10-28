<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmSubscription;
use App\Models\Setting;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $redirect = redirect()->to(url()->previous().'#subscribe')->with('subscribe_sent', true);

        // honeypot
        if ($request->filled('sub_website')) {
            return $redirect;
        }

        $data = $request->validate([
            'sub_email' => ['required', 'string', 'email', 'max:254'],
            'sub_consent' => ['accepted'],
        ], [], [
            'sub_email' => __('site.form_email'),
            'sub_consent' => __('site.form_consent_short'),
        ]);

        $subscriber = Subscriber::firstOrNew(['email' => mb_strtolower(trim($data['sub_email']))]);

        // уже подтверждён и не отписан — ничего не меняем и не раскрываем это
        if ($subscriber->exists && $subscriber->confirmed_at && ! $subscriber->unsubscribed_at) {
            return $redirect;
        }

        $subscriber->fill([
            'locale' => app()->getLocale(),
            'source' => Str::limit(parse_url(url()->previous(), PHP_URL_PATH) ?: '/', 100, ''),
            'consent_at' => now(),
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ])->save();

        defer(fn () => rescue(fn () => Mail::to($subscriber->email)
            ->locale($subscriber->locale)
            ->send(new ConfirmSubscription($subscriber))));

        return $redirect;
    }

    public function confirm(Subscriber $subscriber): View
    {
        if (! $subscriber->confirmed_at && ! $subscriber->unsubscribed_at) {
            $subscriber->confirmed_at = now();
            $subscriber->save();
        }

        return $this->result('confirmed', ['magnetUrl' => Setting::get('lead_magnet_url')]);
    }

    public function unsubscribeForm(string $token): View
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        return $this->result($subscriber->unsubscribed_at ? 'unsubscribed' : 'unsubscribe', ['token' => $token]);
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        if (! $subscriber->unsubscribed_at) {
            $subscriber->unsubscribed_at = now();
            $subscriber->save();
        }

        return $this->result('unsubscribed');
    }

    private function result(string $state, array $data = []): View
    {
        $keys = [
            'confirmed' => ['subscribe_confirmed_title', 'subscribe_confirmed_text'],
            'unsubscribe' => ['unsubscribe_title', 'unsubscribe_text'],
            'unsubscribed' => ['unsubscribed_title', 'unsubscribed_text'],
        ][$state];

        return view('subscription', $data + [
            'state' => $state,
            'title' => __('site.'.$keys[0]),
            'text' => __('site.'.$keys[1]),
            'magnetUrl' => null,
            'token' => null,
        ]);
    }
}
