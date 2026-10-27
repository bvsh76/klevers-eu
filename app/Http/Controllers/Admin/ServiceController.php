<?php

namespace App\Http\Controllers\Admin;

use App\Models\Segment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;

class ServiceController extends ContentController
{
    protected function model(): string
    {
        return Service::class;
    }

    protected function routePrefix(): string
    {
        return 'admin.services';
    }

    protected function labels(): array
    {
        return ['plural' => 'Услуги', 'create' => 'Новая услуга', 'edit' => 'Редактирование услуги'];
    }

    protected function fields(): array
    {
        $fields = parent::fields();

        return array_slice($fields, 0, 2, true)
            + ['intro' => ['Вступление (для карточек)', 'textarea', false]]
            + array_slice($fields, 2, null, true);
    }

    protected function extraRules(?Model $item): array
    {
        return [
            'segments' => ['nullable', 'array'],
            'segments.*' => ['integer', 'exists:segments,id'],
        ];
    }

    protected function extraFormData(Model $item): array
    {
        return [
            'allSegments' => Segment::query()->ordered()->get(),
            'selectedSegments' => array_map('intval', (array) old('segments', $item->exists ? $item->segments()->pluck('segments.id')->all() : [])),
        ];
    }

    protected function saveExtra(Model $item, array $data): void
    {
        $item->segments()->sync($data['segments'] ?? []);
    }

    protected function extraView(): ?string
    {
        return 'admin.services.segments';
    }
}
