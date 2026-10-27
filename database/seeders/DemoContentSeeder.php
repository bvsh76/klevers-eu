<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Page;
use App\Models\Segment;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Демо-контент для local/staging. Тексты и цены — заглушки, не для production.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'home' => [['home', 'home', 'home'], ['Консалтинг и бухгалтерия для вашей фирмы в Эстонии', 'Nõustamine ja raamatupidamine teie Eesti ettevõttele', 'Consulting and accounting for your company in Estonia']],
            'about' => [['o-kompanii', 'meist', 'about'], ['О компании', 'Meist', 'About us']],
            'contacts' => [['kontakty', 'kontakt', 'contacts'], ['Контакты', 'Kontakt', 'Contacts']],
            'privacy' => [['politika-konfidencialnosti', 'privaatsuspoliitika', 'privacy-policy'], ['Политика конфиденциальности', 'Privaatsuspoliitika', 'Privacy policy']],
        ];

        foreach (array_values(array_keys($pages)) as $i => $key) {
            [$slug, $title] = $pages[$key];
            Page::updateOrCreate(['key' => $key], [
                'slug' => $this->t(...$slug),
                'title' => $this->t(...$title),
                'intro' => $key === 'home' ? $this->t('Налоговое, бизнес- и финансовое консультирование и бухгалтерия для начинающих, малого бизнеса и жителей ЕС с эстонской фирмой.', 'Maksu-, äri- ja finantsnõustamine ning raamatupidamine alustajatele, väikeettevõtetele ja Eesti firmaga EL-i elanikele.', 'Tax, business and financial consulting and accounting for start-ups, small businesses and EU residents with an Estonian company.') : null,
                'body' => $this->t('<p>Демо-текст страницы.</p>', '<p>Lehe näidistekst.</p>', '<p>Demo page text.</p>'),
                'meta_title' => $this->t(...$title),
                'is_published' => true,
                'sort' => $i,
            ]);
        }

        $segments = [
            'starters' => [['nachinayushchim', 'algajatele', 'for-starters'], ['Начинающим', 'Algajatele', 'For starters']],
            'small-business' => [['malomu-biznesu', 'vaikeettevotjale', 'small-business'], ['Малому бизнесу', 'Väikeettevõtjale', 'Small business']],
            'eu-residents' => [['zhitelyam-es', 'el-residentidele', 'eu-residents'], ['Жителям ЕС с эстонской фирмой', 'EL residentidele Eesti firmaga', 'EU residents with an Estonian company']],
        ];

        $services = [
            'tax' => [['nalogovoe-konsultirovanie', 'maksunoustamine', 'tax-consulting'], ['Налоговое консультирование', 'Maksunõustamine', 'Tax consulting']],
            'business' => [['biznes-konsultirovanie', 'arinoustamine', 'business-consulting'], ['Бизнес-консультирование', 'Ärinõustamine', 'Business consulting']],
            'finance' => [['finansovoe-konsultirovanie', 'finantsnoustamine', 'financial-consulting'], ['Финансовое консультирование и бюджетирование', 'Finantsnõustamine ja eelarvestamine', 'Financial consulting and budgeting']],
            'accounting' => [['buhgalteriya', 'raamatupidamine', 'accounting'], ['Бухгалтерия', 'Raamatupidamine', 'Accounting']],
        ];

        $segmentModels = [];
        foreach (array_keys($segments) as $i => $key) {
            [$slug, $title] = $segments[$key];
            $segmentModels[$key] = Segment::updateOrCreate(['key' => $key], [
                'slug' => $this->t(...$slug),
                'title' => $this->t(...$title),
                'intro' => $this->t('Короткое описание сегмента.', 'Segmendi lühikirjeldus.', 'Short segment description.'),
                'body' => $this->t('<p>Демо-текст сегмента.</p>', '<p>Segmendi näidistekst.</p>', '<p>Demo segment text.</p>'),
                'is_published' => true,
                'sort' => $i,
            ]);
        }

        $serviceModels = [];
        foreach (array_keys($services) as $i => $key) {
            [$slug, $title] = $services[$key];
            $serviceModels[$key] = Service::updateOrCreate(['key' => $key], [
                'slug' => $this->t(...$slug),
                'title' => $this->t(...$title),
                'intro' => $this->t('Короткое описание услуги.', 'Teenuse lühikirjeldus.', 'Short service description.'),
                'body' => $this->t('<p>Демо-текст услуги.</p>', '<p>Teenuse näidistekst.</p>', '<p>Demo service text.</p>'),
                'is_published' => true,
                'sort' => $i,
            ]);
            $serviceModels[$key]->segments()->sync(array_map(fn ($s) => $s->id, $segmentModels));
        }

        $packages = [
            'start' => [['Старт', 'Start', 'Start'], '49.00', false],
            'business' => [['Бизнес', 'Äri', 'Business'], '149.00', true],
            'custom' => [['Индивидуальный', 'Individuaalne', 'Custom'], null, false],
        ];

        foreach (array_keys($packages) as $i => $key) {
            [$title, $price, $featured] = $packages[$key];
            Package::updateOrCreate(['key' => $key], [
                'service_id' => $serviceModels['accounting']->id,
                'title' => $this->t(...$title),
                'description' => $this->t('Демо-описание пакета.', 'Paketi näidiskirjeldus.', 'Demo package description.'),
                'features' => $this->t(['Пункт 1', 'Пункт 2'], ['Punkt 1', 'Punkt 2'], ['Item 1', 'Item 2']),
                'price' => $price,
                'price_period' => $price === null ? null : 'month',
                'is_featured' => $featured,
                'is_active' => true,
                'sort' => $i,
            ]);
        }
    }

    private function t(mixed $ru, mixed $et, mixed $en): array
    {
        return ['ru' => $ru, 'et' => $et, 'en' => $en];
    }
}
