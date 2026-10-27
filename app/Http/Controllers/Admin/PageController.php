<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;

class PageController extends ContentController
{
    protected function model(): string
    {
        return Page::class;
    }

    protected function routePrefix(): string
    {
        return 'admin.pages';
    }

    protected function labels(): array
    {
        return ['plural' => 'Страницы', 'create' => 'Новая страница', 'edit' => 'Редактирование страницы'];
    }
}
