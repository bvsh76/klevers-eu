<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingController extends Controller
{
    /** @var array<string, array{0: string, 1: string}> */
    private const FIELDS = [
        'whatsapp' => ['WhatsApp — номер в международном формате, например +372 5555 5555', 'input'],
        'telegram' => ['Telegram — имя пользователя (можно с @)', 'input'],
        'lead_email' => ['E-mail для уведомлений о заявках', 'input'],
        'booking_embed' => ['Код встраивания онлайн-записи (Cal.com, Calendly и др.) — вставляется на сайт как есть', 'textarea'],
        'ga4_id' => ['Google Analytics 4 — идентификатор вида G-XXXXXXXXXX', 'input'],
        'lead_magnet_url' => ['Лид-магнит — ссылка на файл (https), выдаётся после подтверждения подписки', 'input'],
    ];

    public function edit(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('admin.settings.edit', [
            'fields' => self::FIELDS,
            'values' => Setting::allValues(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        if (is_string($request->input('ga4_id'))) {
            $request->merge(['ga4_id' => strtoupper(trim($request->input('ga4_id')))]);
        }

        $attributes = [];
        foreach (self::FIELDS as $key => [$label]) {
            $attributes[$key] = Str::before($label, ' —');
        }

        $data = $request->validate([
            'whatsapp' => ['nullable', 'string', 'regex:/^\+?[0-9 ()-]{7,25}$/'],
            'telegram' => ['nullable', 'string', 'regex:/^@?[A-Za-z0-9_]{5,32}$/'],
            'lead_email' => ['nullable', 'email', 'max:255'],
            'booking_embed' => ['nullable', 'string', 'max:20000'],
            'ga4_id' => ['nullable', 'string', 'regex:/^G-[A-Z0-9]{4,20}$/'],
            'lead_magnet_url' => ['nullable', 'url:https', 'max:2000'],
        ], [], $attributes);

        $data['whatsapp'] = filled($data['whatsapp'] ?? null) ? preg_replace('/\D/', '', $data['whatsapp']) : null;
        $data['telegram'] = filled($data['telegram'] ?? null) ? ltrim(trim($data['telegram']), '@') : null;

        foreach (Setting::KEYS as $key) {
            $value = $data[$key] ?? null;
            Setting::put($key, filled($value) ? trim($value) : null);
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Сохранено.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === Role::Admin, 403);
    }
}
