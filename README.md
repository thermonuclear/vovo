# vovo — Order Management API

> Репозиторий: [github.com/thermonuclear/vovo](https://github.com/thermonuclear/vovo)

REST API для управления заказами интернет-магазина запчастей.

## Стек

- **Backend:** Laravel 12, PHP 8.4
- **Database:** MySQL 8
- **Cache/Queue:** Redis 7
- **Web Server:** Nginx 1.27 → PHP-FPM
- **Containerization:** Docker Compose

## Требования

- [Docker](https://www.docker.com/) >= 24.0
- [Docker Compose](https://docs.docker.com/compose/) >= 2.20
- [Make](https://www.gnu.org/software/make/) (опционально)
- 2 GB свободной RAM

## Быстрый старт

### Развёртывание одной командой

```bash
# 1. Клонировать репозиторий
git clone https://github.com/thermonuclear/vovo.git
cd vovo

# 2. Запустить
make up
```

Или через скрипт:

```bash
# Windows
start.bat

# Linux/macOS
chmod +x start.sh && ./start.sh
```

После запуска:
- API: `http://localhost:8080`
- API Docs: `http://localhost:8080/docs`
- phpMyAdmin: `http://localhost:8081`

### Ручная установка

```bash
# 1. Клонировать репозиторий
git clone https://github.com/thermonuclear/vovo.git
cd vovo

# 2. Поднять контейнеры
docker compose up -d --build

# 2. Установить зависимости
docker compose exec php composer install

# 3. Сгенерировать ключ
docker compose exec php php artisan key:generate

# 4. Применить миграции
docker compose exec php php artisan migrate --force

# 5. Заполнить тестовыми данными
docker compose exec php php artisan db:seed
```

## Основные команды

| Команда | Описание |
|---------|----------|
| `make up` | Поднять и настроить проект |
| `make down` | Остановить контейнеры |
| `make shell` | Войти в PHP-контейнер |
| `make migrate` | Применить миграции |
| `make migrate-fresh` | Пересоздать БД |
| `make seed` | Заполнить тестовыми данными |
| `make test` | Запустить тесты |
| `make queue` | Запустить воркер очереди |
| `make pint` | Форматировать код |
| `make clean` | Удалить контейнеры и volumes |

## Переменные окружения

### Корневой `.env` (Docker)

| Переменная | По умолчанию | Описание |
|------------|-------------|----------|
| `APP_NAME` | `vovo` | Имя проекта (контейнеры) |
| `NGINX_PORT` | `8080` | Порт веб-сервера |
| `MYSQL_PORT` | `3306` | Порт MySQL |
| `PHPMYADMIN_PORT` | `8081` | Порт phpMyAdmin |
| `REDIS_PORT` | `6379` | Порт Redis |
| `DB_DATABASE` | `vovo` | Имя БД |
| `DB_USERNAME` | `vovo` | Пользователь БД |
| `DB_PASSWORD` | `secret` | Пароль БД |

### `app/.env` (Laravel)

| Переменная | По умолчанию | Описание |
|------------|-------------|----------|
| `QUEUE_CONNECTION` | `redis` | Драйвер очереди |
| `CACHE_STORE` | `redis` | Драйвер кеша |
| `REDIS_HOST` | `redis` | Хост Redis |
| `EXPORT_ORDER_URL` | `https://httpbin.org/post` | URL для экспорта заказов |

## API Документация

Полная интерактивная документация доступна в Swagger UI:

```
http://localhost:8080/docs
```

OpenAPI spec в формате JSON:

```
http://localhost:8080/docs/openapi.json
```

## Запуск очереди

Очереди работают через Redis. Job `ExportOrderJob` диспатчится при подтверждении заказа.

### Запуск воркера

```bash
make queue          # Запустить воркер
make queue-failed   # Показать неудачные job-ы
make queue-retry    # Повторить все неудачные job-ы
make queue-flush    # Очистить все job-ы из очереди
```

### Redis database mapping

| DB | Назначение |
|----|-----------|
| 0 | Default (сессии, rate limiting) |
| 1 | Cache |
| 2 | Queue |

## Тесты

```bash
make test
# или
docker compose exec php php artisan test
```

## Структура проекта

```
app/
├── app/
│   ├── DTO/              # Data Transfer Objects
│   ├── Enums/            # OrderStatus enum
│   ├── Events/           # OrderConfirmed event
│   ├── Jobs/             # ExportOrderJob
│   ├── Listeners/        # ExportOrderListener
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   ├── Requests/     # Form requests
│   │   └── Resources/    # API resources
│   ├── Models/           # Eloquent models
│   ├── Repositories/     # Data access layer
│   └── Services/         # Business logic
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docs/
│   └── openapi.json      # OpenAPI 3.0.3 specification
├── routes/
│   └── api.php
└── tests/
    ├── Feature/
    └── Unit/
```

## Устранение проблем

### Контейнеры не запускаются

```bash
# Пересобрать контейнеры
docker compose build --no-cache

# Очистить volumes и начать заново
make clean
make up
```

### Ошибка подключения к БД

```bash
# Проверить что MySQL здоров
docker compose exec mysql mysqladmin ping -h localhost -u root -prootsecret

# Перезапустить MySQL
docker compose restart mysql
```

### Очередь не обрабатывает задачи

```bash
# Проверить что Redis доступен
docker compose exec redis redis-cli ping

# Запустить воркер
make queue
```

### Тесты падают

```bash
# Очистить кеш конфига
docker compose exec php php artisan config:clear

# Перезапустить тесты
make test
```
