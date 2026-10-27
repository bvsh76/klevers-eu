<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.leads.index', [
            'items' => $this->filtered($request)->with('service', 'package')->latest('id')->paginate(50)->withQueryString(),
            'statuses' => LeadStatus::cases(),
        ]);
    }

    public function show(string $id): View
    {
        return view('admin.leads.show', [
            'lead' => Lead::with('service', 'package')->findOrFail($id),
            'statuses' => LeadStatus::cases(),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(LeadStatus::class)]]);
        Lead::findOrFail($id)->update($data);

        return back()->with('status', 'Статус обновлён.');
    }

    public function export(Request $request): StreamedResponse
    {
        $default = config('locales.default');

        return Csv::download(
            'leads-'.now(config('app.display_timezone'))->format('Y-m-d').'.csv',
            ['ID', 'Дата', 'Статус', 'Имя', 'E-mail', 'Телефон', 'Язык', 'Услуга', 'Пакет', 'Источник', 'Сообщение', 'Согласие'],
            $this->filtered($request)->with('service', 'package'),
            fn (Lead $lead) => [
                $lead->id,
                Csv::date($lead->created_at),
                $lead->status->label(),
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->locale,
                $lead->service?->getTranslation('title', $default, false),
                $lead->package?->getTranslation('title', $default, false),
                $lead->source,
                $lead->message,
                Csv::date($lead->consent_at),
            ],
        );
    }

    private function filtered(Request $request): Builder
    {
        return Lead::query()
            ->when(LeadStatus::tryFrom((string) $request->query('status')), fn (Builder $q, LeadStatus $s) => $q->where('status', $s))
            ->when(trim((string) $request->query('q')), function (Builder $q, string $term) {
                $q->where(fn (Builder $w) => $w->whereLike('name', "%{$term}%")->orWhereLike('email', "%{$term}%")->orWhereLike('phone', "%{$term}%"));
            });
    }
}
