# Комментарии

Небольшое приложение для обсуждения на PHP 8.2, CodeIgniter 4, MySQL 8, jQuery 3 и Bootstrap 4. Исходное тестовое задание: [vad23klev/fullstack-test](https://github.com/vad23klev/fullstack-test).

[Открыть демо](https://fullstack-comments.vercel.app/). Публичная версия работает на Vercel с Neon Postgres; локальная сборка по заданию использует MySQL 8.

## Запуск

Нужны Docker и Docker Compose. Перед первым запуском создайте локальный файл настроек:

```bash
cp .env.example .env
```

Укажите свой `MYSQL_ROOT_PASSWORD` в `.env`, затем выполните:

```bash
docker compose up -d --build
docker compose exec web composer install
docker compose exec web php spark migrate
```

Сайт откроется по адресу <http://localhost/>. Adminer доступен на <http://localhost:8080/> (сервер `db`, пользователь `root`, пароль из `.env`). Для повторного запуска достаточно `docker compose up -d`.

В старой версии Compose можно использовать `docker-compose` вместо `docker compose`.

Тесты запускаются командой `docker compose exec web ./vendor/bin/phpunit --no-coverage`.

## Возможности

- Добавление и удаление без перезагрузки страницы.
- Постраничный список: три комментария на странице, выбор страницы и переходы вперёд/назад.
- Сортировка по ID или времени добавления в обоих направлениях.
- Мгновенная проверка email в форме и повторная проверка всех полей на сервере.
- Выбранная автором дата хранится в поле `date` строкой `YYYY-MM-DD`; время фактического добавления хранится отдельно в `created_at` для корректной сортировки.
- Защита POST-запросов CSRF-токеном; текст пользователя выводится без интерпретации HTML.

Таблица создаётся миграцией `CreateComments`. Страница и API используют маршруты `/`, `GET /comments`, `POST /comments`, `POST /comments/{id}/delete`. Формат ошибок валидации — JSON с HTTP 422.
