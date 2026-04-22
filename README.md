# Vovo — Product Catalog API

REST API для каталога товаров с поиском, фильтрами и пагинацией.

## Стек

- **Backend:** Laravel 12, PHP 8.4
- **Database:** MySQL 8
- **Cache/Queue:** Redis 7
- **Web Server:** Nginx 1.27 → PHP-FPM
- **Containerization:** Docker Compose

## Требования

- [Docker](https://www.docker.com/) >= 24.0
- [Docker Compose](https://docs.docker.com/compose/) >= 2.20
- 2 GB свободной RAM

## Быстрый старт

### Развёртывание одной командой

```bash
# 1. Клонировать репозиторий
git clone https://github.com/thermonuclear/vovo.git
cd vovo

# 2. Запустить
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

# 3. Установить зависимости
docker compose exec php composer install

# 4. Сгенерировать ключ
docker compose exec php php artisan key:generate

# 5. Применить миграции и заполнить данными
docker compose exec php php artisan migrate --force
docker compose exec php php artisan db:seed
```

## Основные команды

| Команда | Описание |
|---------|----------|
| `docker compose up -d --build` | Поднять контейнеры |
| `docker compose down` | Остановить контейнеры |
| `docker compose exec php bash` | Войти в PHP-контейнер |
| `docker compose exec php php artisan migrate` | Применить миграции |
| `docker compose exec php php artisan migrate:fresh --seed` | Пересоздать БД |
| `docker compose exec php php artisan db:seed` | Заполнить тестовыми данными |
| `docker compose exec php php artisan test` | Запустить тесты |
| `docker compose exec php php artisan pint` | Форматировать код |

## API

### Поиск товаров

```
GET /api/products
```

**Параметры запроса:**

| Параметр | Тип | Описание |
|----------|-----|----------|
| `q` | string | Поиск по названию (FULLTEXT / LIKE) |
| `price_from` | number | Минимальная цена |
| `price_to` | number | Максимальная цена |
| `category_id` | integer | Фильтр по категории |
| `in_stock` | boolean | Только товары в наличии |
| `rating_from` | number | Минимальный рейтинг (0–5) |
| `sort` | string | Сортировка: `price_asc`, `price_desc`, `rating_desc`, `newest` |
| `per_page` | integer | Товаров на странице (1–100, по умолчанию 15) |
| `page` | integer | Номер страницы |

**Пример:**

```bash
curl "http://localhost:8080/api/products?q=смартфон&price_from=100&price_to=500&sort=price_asc&per_page=10"
```

**Ответ:**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Смартфон Galaxy Pro",
      "price": "299.99",
      "category": { "id": 1, "name": "Электроника", "slug": "elektronika" },
      "in_stock": true,
      "rating": 4.5,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 100
  },
  "links": {
    "first": "...",
    "last": "...",
    "next": "...",
    "prev": null
  }
}
```

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

## Тесты

```bash
docker compose exec php php artisan test
```

**Особенности тестирования:**
- Feature-тесты используют `DatabaseTransactions` на MySQL
- FULLTEXT поиск тестируется на засиженных данных
- Остальные тесты изолируются через фильтрацию по уникальной категории
- Unit-тесты используют моки (Mockery)

## Структура проекта

```
vovo/
├── app/                          # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   │   └── ProductController.php
│   │   │   ├── Requests/
│   │   │   │   └── FilterProductsRequest.php
│   │   │   └── Resources/
│   │   │       └── ProductResource.php
│   │   ├── Models/
│   │   │   ├── Category.php
│   │   │   └── Product.php
│   │   ├── Repositories/
│   │   │   ├── Contracts/
│   │   │   │   └── ProductRepositoryInterface.php
│   │   │   └── ProductRepository.php
│   │   └── Services/
│   │       └── ProductService.php
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php
│   └── tests/
│       ├── Feature/Api/
│       │   ├── ProductControllerTest.php
│       │   └── ProductSearchTest.php
│       └── Unit/Services/
│           └── ProductServiceTest.php
├── docker/
│   ├── php/                      # PHP-FPM Dockerfile
│   └── nginx/                    # Nginx Dockerfile
├── mysql/init/                   # DB init scripts
├── docker-compose.yml
├── .env.example                  # Docker variables template
├── app/.env.example              # Laravel variables template
├── start.bat / start.sh          # One-command setup
└── AGENTS.md                     # Development guidelines
```

## Архитектура

```
HTTP Request → FilterProductsRequest (валидация)
    → ProductController (тонкий HTTP-слой)
        → ProductService (нормализация параметров)
            → ProductRepository (Eloquent query + scopes)
                → Product Model (MySQL: FULLTEXT/LIKE + WHERE + ORDER BY)
    ← ProductResource (трансформация в JSON)
← JSON Response { data: [...], meta: {...}, links: {...} }
```

## Устранение проблем

### Контейнеры не запускаются

```bash
# Пересобрать контейнеры
docker compose build --no-cache

# Очистить volumes и начать заново
docker compose down -v
docker compose up -d --build
```

### Ошибка подключения к БД

```bash
# Проверить что MySQL здоров
docker compose exec mysql mysqladmin ping -h localhost -u root -prootsecret

# Перезапустить MySQL
docker compose restart mysql
```

### Тесты падают

```bash
# Очистить кеш конфига
docker compose exec php php artisan config:clear

# Перезапустить тесты
docker compose exec php php artisan test
```
