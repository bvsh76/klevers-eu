# klevers.eu

Сайт компании Klevers (Эстония). Языки: ru (по умолчанию), et, en.

Стек: Laravel 13 · PHP 8.4 · PostgreSQL 17 · Redis 7 · Docker. Деплой: Laravel Cloud.

## Требования

- Docker Desktop
- Git

## Первый запуск

> В Windows PowerShell 5.1 команды разделяются `;`, а не `&&`.

~~~
git clone https://github.com/bvsh76/klevers-eu.git
cd klevers-eu
Copy-Item .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
~~~

Сайт: http://localhost:8090

В `.env` для локального окружения:

| Ключ | Значение |
|---|---|
| DB_CONNECTION / DB_HOST / DB_PORT | pgsql / db / 5432 |
| DB_DATABASE / DB_USERNAME / DB_PASSWORD | klevers / klevers / secret |
| SESSION_DRIVER, CACHE_STORE, QUEUE_CONNECTION | redis |
| REDIS_HOST / REDIS_PORT | redis / 6379 |

## Контейнеры и порты

| Сервис | Образ | Порт на хосте |
|---|---|---|
| web | nginx 1.27 | 8090 |
| app | php-fpm 8.4 | — |
| db | postgres 17 | 5433 |
| redis | redis 7 | 6380 |

PHP-FPM локально работает от root — иначе нет записи в `storage` на Windows-монтировании. Только для локальной среды.

## Повседневное

~~~
docker compose up -d
docker compose exec app php artisan test
docker compose down
~~~

## Настройки

- Часовой пояс приложения и БД — UTC. Для вывода дат пользователю: `config('app.display_timezone')` (Europe/Tallinn).
- Локаль по умолчанию — ru, резервная — en.

## Ветки и деплой

- `staging` — рабочая ветка → staging в Laravel Cloud
- `main` — production
- CI (GitHub Actions): тесты на каждый push в `staging`/`main` и на PR
## Laravel Cloud

- Организация: borissshipunov, приложение: klevers-eu, регион: EU Central (Frankfurt)
- staging — ветка staging — https://klevers-eu-staging-2dkkhb.laravel.cloud
  - БД: Serverless Postgres 17, кластер klevers-staging (Dev)
  - сессии, кеш, очереди: database (заданы в Custom environment variables)
- production — ветка main — https://klevers-eu-production-klmf2r.laravel.cloud
  - БД: Serverless Postgres 17, кластер klevers-production (Dev, на С6 перевести на Prod)
  - кеш: Valkey klevers-production-cache (Flex 250 MB); сессии, кеш, очереди: redis
- Push to deploy включён в обоих окружениях
- Cloud не ждёт GitHub Actions: в main сливаем только то, что прошло CI на staging
- Build: composer install; npm закомментирован до С4. Deploy: php artisan migrate --force
- Секреты (APP_KEY, пароли БД) в чат и скриншоты не попадают
## Мультиязычность

- Языки и их порядок в переключателе задаются в `config/locales.php` (`supported`). Язык по умолчанию — `APP_LOCALE` (ru), fallback — `APP_FALLBACK_LOCALE` (en).
- Все страницы живут под префиксом `/{locale}`: группа в `routes/web.php`, middleware `locale` → `App\Http\Middleware\SetLocale`. Запрос на `/` даёт 302 на язык по умолчанию, неизвестный префикс — 404.
- Внутри группы `route('name')` сам подставляет текущий язык. Ссылка на эту же страницу на другом языке — `App\Support\LocaleUrl::to('et')`.
- Язык по умолчанию брать из `config('locales.default')`, а не из `config('app.locale')`: `App::setLocale()` меняет `app.locale` на каждом запросе.
- Layout `<x-layouts.app>` выводит `<html lang>`, `hreflang` и `x-default`. Переключатель языков — `<x-language-switcher />`.
- Системные переводы (валидация, пароли, пагинация, HTTP-статусы) берутся из пакета laravel-lang (require-dev) и обновляются через `php artisan lang:update`.
- Свои строки сайта лежат только в `lang/{ru,et,en}/site.php`, пакет их не трогает. Каталог `lang/` исключён из Pint (`pint.json`).
- Чтобы добавить язык: строка в `config/locales.php` → `php artisan lang:add <код>` → файл `lang/<код>/site.php`.
## SEO и страницы ошибок

- Layout `<x-layouts.app>` принимает `title`, `description` и `alternates`. При `alternates=true` (по умолчанию) выводятся canonical (текущий URL без query), `hreflang` на все языки и `x-default`.
- `LocaleUrl::to($locale, $withQuery = true)`: для `hreflang` query отбрасывается, переключатель языков его сохраняет.
- `/robots.txt` генерируется маршрутом. Индексация разрешена только при `APP_INDEXABLE=true` (`config/seo.php`), по умолчанию закрыто всё. Включать только на production.
- `/sitemap.xml` перечисляет локализованные страницы со ссылками на языковые версии. Список страниц пока задан в `routes/web.php`, с С3 будет строиться из БД.
- Страницы 404/500 лежат в `resources/views/errors/`. Язык берётся из первого сегмента URL, при неизвестном сегменте используется язык по умолчанию.
