<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Page;
use App\Models\Service;
use App\Models\Subscriber;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Новые заявки' => Lead::where('status', LeadStatus::New)->count(),
                'Всего заявок' => Lead::count(),
                'Подписчики' => Subscriber::active()->count(),
                'Страницы' => Page::count(),
                'Услуги' => Service::count(),
                'Пакеты' => Package::count(),
            ],
        ]);
    }
}
