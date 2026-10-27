<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Общий CRUD для сущностей с переводимыми полями (Page, Segment, Service).
 */
abstract class ContentController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    abstract protected function routePrefix(): string;

    /** @return array{plural: string, create: string, edit: string} */
    abstract protected function labels(): array;

    /** @return array<string, array{0: string, 1: string, 2: bool}> поле => [подпись, input|textarea, обязательно для языка по умолчанию] */
    protected function fields(): array
    {
        return [
            'title' => ['Заголовок', 'input', true],
            'slug' => ['Слаг (URL); пусто — из заголовка', 'input', true],
            'body' => ['Текст (HTML)', 'textarea', false],
            'meta_title' => ['Meta title', 'input', false],
            'meta_description' => ['Meta description', 'textarea', false],
        ];
    }

    /** Доп. правила валидации (связи и т. п.). */
    protected function extraRules(?Model $item): array
    {
        return [];
    }

    /** Доп. данные для формы. */
    protected function extraFormData(Model $item): array
    {
        return [];
    }

    protected function saveExtra(Model $item, array $data): void {}

    protected function extraView(): ?string
    {
        return null;
    }

    public function index(): View
    {
        return view('admin.content.index', $this->shared() + [
            'items' => $this->model()::query()->ordered()->paginate(50),
        ]);
    }

    public function create(): View
    {
        return $this->form(new ($this->model()));
    }

    public function store(Request $request): RedirectResponse
    {
        $item = new ($this->model());
        $this->persist($item, $this->validated($request, null));

        return redirect()->route($this->routePrefix().'.edit', $item->id)->with('status', 'Сохранено.');
    }

    public function edit(string $id): View
    {
        return $this->form($this->model()::findOrFail($id));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $item = $this->model()::findOrFail($id);
        $this->persist($item, $this->validated($request, $item));

        return redirect()->route($this->routePrefix().'.edit', $item->id)->with('status', 'Сохранено.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $item = $this->model()::findOrFail($id);

        if (filled($item->key)) {
            return back()->withErrors(['delete' => 'Системную запись (с ключом) удалить нельзя.']);
        }

        $item->delete();

        return redirect()->route($this->routePrefix().'.index')->with('status', 'Удалено.');
    }

    protected function form(Model $item): View
    {
        return view('admin.content.form', $this->shared() + $this->extraFormData($item) + [
            'item' => $item,
            'extraView' => $this->extraView(),
        ]);
    }

    protected function shared(): array
    {
        return [
            'fields' => $this->fields(),
            'locales' => $this->locales(),
            'default' => config('locales.default'),
            'prefix' => $this->routePrefix(),
            'labels' => $this->labels(),
        ];
    }

    /** @return array<int, string> */
    protected function locales(): array
    {
        $supported = (array) config('locales.supported');

        return array_is_list($supported) ? $supported : array_keys($supported);
    }

    protected function validated(Request $request, ?Model $item): array
    {
        $locales = $this->locales();
        $default = config('locales.default');
        $table = (new ($this->model()))->getTable();

        if (array_key_exists('slug', $this->fields())) {
            $slugs = (array) $request->input('slug', []);
            $titles = (array) $request->input('title', []);
            foreach ($locales as $locale) {
                if (blank($slugs[$locale] ?? null) && filled($titles[$locale] ?? null)) {
                    $slugs[$locale] = Str::slug($titles[$locale], '-', $locale);
                }
            }
            $request->merge(['slug' => $slugs]);
        }

        $rules = [
            'key' => ['nullable', 'alpha_dash', 'max:50', Rule::unique($table, 'key')->ignore($item?->id)],
            'is_published' => ['boolean'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
        $attributes = ['key' => 'Ключ', 'sort' => 'Порядок'];

        foreach ($this->fields() as $name => [$label, $type, $required]) {
            foreach ($locales as $locale) {
                $rule = [$required && $locale === $default ? 'required' : 'nullable', 'string', $type === 'textarea' ? 'max:65000' : 'max:255'];

                if ($name === 'slug') {
                    $rule[] = 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
                    $rule[] = Rule::unique($table, "slug->{$locale}")->ignore($item?->id);
                }

                $rules["{$name}.{$locale}"] = $rule;
                $attributes["{$name}.{$locale}"] = Str::before($label, ';').' ('.strtoupper($locale).')';
            }
        }

        return $request->validate($rules + $this->extraRules($item), [], $attributes);
    }

    protected function persist(Model $item, array $data): void
    {
        $item->key = $data['key'] ?? null;
        $item->is_published = (bool) ($data['is_published'] ?? false);
        $item->sort = (int) ($data['sort'] ?? 0);

        foreach (array_keys($this->fields()) as $name) {
            $values = array_filter((array) ($data[$name] ?? []), fn ($value) => filled($value));
            $item->replaceTranslations($name, $values);
        }

        $item->save();
        $this->saveExtra($item, $data);
    }
}
