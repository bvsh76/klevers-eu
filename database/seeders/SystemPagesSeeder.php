<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Системные страницы, на которые опирается код (главная, CTA, формы, подвал).
 * Безопасен для production: создаёт только отсутствующие страницы и ничего не перезаписывает.
 */
class SystemPagesSeeder extends Seeder
{
    /** key => [[slug ru, et, en], [title ru, et, en]] */
    public const PAGES = [
        'home' => [['home', 'home', 'home'], ['Консалтинг и бухгалтерия для вашей фирмы в Эстонии', 'Nõustamine ja raamatupidamine teie Eesti ettevõttele', 'Consulting and accounting for your company in Estonia']],
        'about' => [['o-kompanii', 'meist', 'about'], ['О компании', 'Meist', 'About us']],
        'contacts' => [['kontakty', 'kontakt', 'contacts'], ['Контакты', 'Kontakt', 'Contacts']],
        'privacy' => [['politika-konfidencialnosti', 'privaatsuspoliitika', 'privacy-policy'], ['Политика конфиденциальности', 'Privaatsuspoliitika', 'Privacy policy']],
    ];

    public function run(): void
    {
        foreach (array_keys(self::PAGES) as $i => $key) {
            [$slug, $title] = self::PAGES[$key];

            Page::firstOrCreate(['key' => $key], [
                'slug' => self::t(...$slug),
                'title' => self::t(...$title),
                'meta_title' => self::t(...$title),
                'is_published' => true,
                'sort' => $i,
            ]);
        }
    }

    /** @return array{ru: string, et: string, en: string} */
    public static function t(string $ru, string $et, string $en): array
    {
        return ['ru' => $ru, 'et' => $et, 'en' => $en];
    }
}
