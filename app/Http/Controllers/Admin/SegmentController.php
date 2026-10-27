<?php

namespace App\Http\Controllers\Admin;

use App\Models\Segment;

class SegmentController extends ContentController
{
    protected function model(): string
    {
        return Segment::class;
    }

    protected function routePrefix(): string
    {
        return 'admin.segments';
    }

    protected function labels(): array
    {
        return ['plural' => 'Сегменты', 'create' => 'Новый сегмент', 'edit' => 'Редактирование сегмента'];
    }
}
