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