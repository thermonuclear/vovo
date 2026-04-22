# Project: Vovo

## Stack
- **Backend:** Laravel 12, PHP 8.4
- **Database:** MySQL 8
- **Cache/Queue:** Redis 7
- **Web Server:** Nginx 1.27 → PHP-FPM
- **Containerization:** Docker Compose

## Project Structure
```
vovo/
├── app/                     # Laravel application code
│   ├── app/                 # application logic
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Providers/
│   │   └── Services/
│   ├── bootstrap/           # framework bootstrap
│   ├── config/              # configuration files
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   ├── public/              # document root
│   ├── resources/
│   │   ├── views/
│   │   ├── css/
│   │   └── js/
│   ├── routes/
│   │   ├── web.php
│   │   └── console.php
│   ├── storage/             # logs, cache, sessions
│   └── tests/
├── docker/
│   ├── php/                 # PHP-FPM Dockerfile + php.ini
│   └── nginx/               # Nginx Dockerfile + default.conf
├── mysql/init/              # DB initialization scripts
├── docker-compose.yml
├── .env                     # Docker variables (gitignore)
├── .env.example             # Docker variables template
├── app/.env                 # Laravel variables (gitignore)
├── app/.env.example         # Laravel variables template
├── start.bat / start.sh
├── Makefile
└── AGENTS.md
```

## Key Commands
- `make up` — build and start all containers
- `make down` — stop containers
- `make shell` — enter PHP container
- `make artisan ARGS="migrate"` — run artisan commands
- `make migrate` — run migrations
- `make migrate-fresh` — reset and re-run all migrations
- `make seed` — run database seeders
- `make test` — run PHPUnit tests
- `docker compose exec php composer <cmd>` — run composer
- `docker compose exec php php artisan <cmd>` — run artisan commands

## Conventions
- Two `.env` files: root for Docker, `app/.env` for Laravel
- All development happens inside Docker containers
- Xdebug enabled on port 9003
- Redis used for cache, sessions, and queues
- Database charset: `utf8mb4`, collation: `utf8mb4_unicode_ci`
- Session driver: `file`, Cache store: `file`, Queue: `sync` (dev)

## Coding Standards
- Follow Laravel best practices and framework conventions
- **DRY** — Don't Repeat Yourself: extract common logic into reusable methods, services, or traits
- **KISS** — Keep It Simple, Stupid: prefer simple, readable solutions over clever or complex ones
- **SOLID** — apply principles where appropriate:
  - Single Responsibility: one class = one responsibility
  - Open/Closed: extend behavior via composition, not modification
  - Liskov Substitution: subclasses must be substitutable for their base classes
  - Interface Segregation: prefer small, focused interfaces
  - Dependency Inversion: depend on abstractions, not concretions
- **Clean Code**:
  - Meaningful names for variables, methods, and classes
  - Small, focused methods (ideally < 20 lines)
  - Minimal comments — code should be self-documenting
  - Consistent formatting (use `pint` — `make artisan ARGS="pint"`)
  - Early returns to reduce nesting
  - Type hints and return types where possible

## Architecture
- **Controllers** — handle HTTP requests/responses only, delegate to services
- **Services** — contain business logic, orchestrate repositories and models
- **Repositories** — data access layer, encapsulate all DB queries and model operations
- **Models** — Eloquent models for entity representation and relationships
- **Requests** — form request validation classes
- **Resources** — API response transformers
- Controllers must NOT directly call models for data operations
- Use dependency injection via Laravel container for service/repository injection
- Flow: Controller → Service → Repository → Model (Eloquent) → DB
- Use Laravel's built-in features: validation, authorization, events, queues

| Область | Требования |
|---------|-----------|
| Архитектура | Service Layer, тонкие контроллеры, разделение ответственности |
| Eloquent | Связи, скоупы, eager loading, отсутствие N+1 |
| API | Resource-классы, консистентные ответы, HTTP-коды |
| Git | Логичные коммиты |
| Код | Типизация, читаемость, чистота |

## Database
- Migrations define schema — never modify existing migrations, create new ones
- Use seeders for initial/test data
- Foreign keys and constraints where applicable
- Soft deletes for entities that should not be permanently removed

## Testing
- Feature-тесты используют `DatabaseTransactions` на MySQL (БД: `vovo`)
- Тесты работают на основной БД — FULLTEXT поиск поддерживается
- Для FULLTEXT тестов используются существующие засиженные данные
- Остальные тесты изолируются через фильтрацию по уникальной категории
- Unit-тесты без работы с БД используют моки (Mockery)
