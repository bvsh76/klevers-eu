<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Page;
use App\Models\Segment;
use App\Models\Service;
use App\Support\LocaleUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentController extends Controller
{
    private const SECTIONS = [
        'services' => Service::class,
        'segments' => Segment::class,
    ];

    public function home(): View
    {
        $locale = app()->getLocale();
        $page = Page::published()->where('key', 'home')->first();

        if ($page) {
            request()->attributes->set(LocaleUrl::ATTRIBUTE, $page);
        }

        $visible = fn ($items) => $items->filter(fn (Model $item) => $item->localizedUrl($locale) !== null)->values();

        return view('home', [
            'page' => $page,
            'locale' => $locale,
            'services' => $visible(Service::published()->ordered()->get()),
            'segments' => $visible(Segment::published()->ordered()->get()),
            'packages' => Package::active()->where('is_featured', true)->ordered()->with('service')->get()
                ->filter(fn (Package $package) => $package->service?->localizedUrl($locale) !== null
                    && filled($package->getTranslation('title', $locale, false)))
                ->values(),
            'contactUrl' => $this->contactUrl($locale),
        ]);
    }

    public function page(string $slug): View|RedirectResponse
    {
        $page = $this->find(Page::class, $slug);

        if ($page->key === 'home') {
            return redirect()->route('home', status: 301);
        }

        return $this->show($page);
    }

    public function section(string $section, string $slug): View
    {
        $locale = app()->getLocale();
        $key = collect(config('locales.sections'))->search(fn (array $prefixes) => ($prefixes[$locale] ?? null) === $section);

        abort_unless($key !== false && isset(self::SECTIONS[$key]), 404);

        return $this->show($this->find(self::SECTIONS[$key], $slug));
    }

    /** @param class-string<Model> $class */
    private function find(string $class, string $slug): Model
    {
        $locale = app()->getLocale();
        $item = $class::published()->whereSlug($slug, $locale)->firstOrFail();

        abort_if($item->localizedUrl($locale) === null, 404);

        return $item;
    }

    private function show(Model $item): View
    {
        $locale = app()->getLocale();
        request()->attributes->set(LocaleUrl::ATTRIBUTE, $item);

        $visible = fn ($items) => $items->filter(fn (Model $related) => $related->localizedUrl($locale) !== null)->values();

        return view('content', [
            'item' => $item,
            'locale' => $locale,
            'packages' => $item instanceof Service
                ? Package::active()->where('service_id', $item->id)->ordered()->get()
                    ->filter(fn (Package $package) => filled($package->getTranslation('title', $locale, false)))
                    ->values()
                : collect(),
            'related' => match (true) {
                $item instanceof Service => $visible(Segment::published()->ordered()->whereIn('id', $item->segments()->pluck('segments.id'))->get()),
                $item instanceof Segment => $visible(Service::published()->ordered()->whereIn('id', $item->services()->pluck('services.id'))->get()),
                default => collect(),
            },
            'contactUrl' => $this->contactUrl($locale),
        ]);
    }

    private function contactUrl(string $locale): ?string
    {
        return Page::published()->where('key', 'contacts')->first()?->localizedUrl($locale);
    }
}
