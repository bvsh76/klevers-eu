<?php

namespace App\Http\Controllers;

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
        request()->attributes->set(LocaleUrl::ATTRIBUTE, $item);

        return view('content', ['item' => $item, 'locale' => app()->getLocale()]);
    }
}
