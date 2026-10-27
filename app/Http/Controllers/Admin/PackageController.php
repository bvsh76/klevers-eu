<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PricePeriod;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageController extends Controller
{
    /** @var array<string, array{0: string, 1: string, 2: bool}> */
    private const FIELDS = [
        'title' => ['Название', 'input', true],
        'description' => ['Описание', 'textarea', false],
        'features' => ['Состав пакета — по одному пункту в строке', 'lines', false],
    ];

    public function index(): View
    {
        return view('admin.packages.index', $this->shared() + [
            'items' => Package::query()->with('service')->ordered()->paginate(50),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Package(['is_active' => true]));
    }

    public function store(Request $request): RedirectResponse
    {
        $package = new Package;
        $this->persist($package, $this->validated($request, null));

        return redirect()->route('admin.packages.edit', $package->id)->with('status', 'Сохранено.');
    }

    public function edit(string $id): View
    {
        return $this->form(Package::findOrFail($id));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $package = Package::findOrFail($id);
        $this->persist($package, $this->validated($request, $package));

        return redirect()->route('admin.packages.edit', $package->id)->with('status', 'Сохранено.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $package = Package::findOrFail($id);

        if (filled($package->key)) {
            return back()->withErrors(['delete' => 'Системную запись (с ключом) удалить нельзя.']);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')->with('status', 'Удалено.');
    }

    private function form(Package $package): View
    {
        return view('admin.packages.form', $this->shared() + [
            'item' => $package,
            'services' => Service::query()->ordered()->get(),
            'periods' => PricePeriod::cases(),
        ]);
    }

    private function shared(): array
    {
        $supported = (array) config('locales.supported');

        return [
            'fields' => self::FIELDS,
            'locales' => array_is_list($supported) ? $supported : array_keys($supported),
            'default' => config('locales.default'),
        ];
    }

    private function validated(Request $request, ?Package $package): array
    {
        if (is_string($request->input('price'))) {
            $request->merge(['price' => str_replace([' ', ','], ['', '.'], trim($request->input('price')))]);
        }

        $shared = $this->shared();
        $rules = [
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'key' => ['nullable', 'alpha_dash', 'max:50', Rule::unique('packages', 'key')->ignore($package?->id)],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'price_period' => ['nullable', 'required_with:price', Rule::enum(PricePeriod::class)],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
        $attributes = ['service_id' => 'Услуга', 'key' => 'Ключ', 'price' => 'Цена', 'price_period' => 'Период', 'sort' => 'Порядок'];

        foreach (self::FIELDS as $name => [$label, $type, $required]) {
            foreach ($shared['locales'] as $locale) {
                $rules["{$name}.{$locale}"] = [
                    $required && $locale === $shared['default'] ? 'required' : 'nullable',
                    'string',
                    $type === 'input' ? 'max:255' : 'max:65000',
                ];
                $attributes["{$name}.{$locale}"] = Str::before($label, ' —').' ('.strtoupper($locale).')';
            }
        }

        return $request->validate($rules, [], $attributes);
    }

    private function persist(Package $package, array $data): void
    {
        $package->service_id = $data['service_id'] ?? null;
        $package->key = $data['key'] ?? null;
        $package->price = filled($data['price'] ?? null) ? $data['price'] : null;
        $package->price_period = $package->price === null ? null : ($data['price_period'] ?? null);
        $package->is_featured = (bool) ($data['is_featured'] ?? false);
        $package->is_active = (bool) ($data['is_active'] ?? false);
        $package->sort = (int) ($data['sort'] ?? 0);

        foreach (self::FIELDS as $name => [, $type]) {
            $values = [];
            foreach ((array) ($data[$name] ?? []) as $locale => $value) {
                if ($type === 'lines') {
                    $value = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $value)), 'filled'));
                }
                if (filled($value)) {
                    $values[$locale] = $value;
                }
            }
            $package->replaceTranslations($name, $values);
        }

        $package->save();
    }
}
