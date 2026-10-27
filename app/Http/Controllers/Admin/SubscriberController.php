<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.subscribers.index', [
            'items' => $this->filtered($request)->latest('id')->paginate(50)->withQueryString(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        return Csv::download(
            'subscribers-'.now(config('app.display_timezone'))->format('Y-m-d').'.csv',
            ['ID', 'E-mail', 'Язык', 'Источник', 'Подписан', 'Согласие', 'Подтверждён', 'Отписан'],
            $this->filtered($request),
            fn (Subscriber $s) => [
                $s->id,
                $s->email,
                $s->locale,
                $s->source,
                Csv::date($s->created_at),
                Csv::date($s->consent_at),
                Csv::date($s->confirmed_at),
                Csv::date($s->unsubscribed_at),
            ],
        );
    }

    private function filtered(Request $request): Builder
    {
        return Subscriber::query()
            ->when($request->query('state') === 'active', fn (Builder $q) => $q->whereNull('unsubscribed_at'))
            ->when($request->query('state') === 'unsubscribed', fn (Builder $q) => $q->whereNotNull('unsubscribed_at'))
            ->when(trim((string) $request->query('q')), fn (Builder $q, string $term) => $q->whereLike('email', "%{$term}%"));
    }
}
