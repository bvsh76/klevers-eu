<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $redirect = redirect()->to(url()->previous().'#lead-form')->with('lead_sent', true);

        // honeypot: бот заполнил скрытое поле — делаем вид, что всё прошло
        if ($request->filled('website')) {
            return $redirect;
        }

        $data = $request->validated();

        Lead::create([
            'name' => trim($data['name']),
            'email' => $data['email'],
            'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
            'message' => filled($data['message'] ?? null) ? trim($data['message']) : null,
            'service_id' => $data['service_id'] ?? null,
            'package_id' => $data['package_id'] ?? null,
            'locale' => app()->getLocale(),
            'source' => Str::limit(parse_url(url()->previous(), PHP_URL_PATH) ?: '/', 100, ''),
            'consent_at' => now(),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'utm' => $request->session()->get('utm'),
        ]);

        return $redirect;
    }
}
