@echo off
echo Checking Docker...
docker --version >nul 2>&1
if errorlevel 1 (
    echo Docker is not installed or not running. Please install Docker Desktop.
    pause
    exit /b 1
)

echo Starting Vovo project...

docker compose up -d --build

echo Waiting for MySQL to be ready...
timeout /t 10 /nobreak >nul

echo Installing dependencies...
docker compose exec php composer install --no-interaction

echo Copying environment files...
docker compose exec php sh -c "[ -f .env ] || cp .env.example .env"

echo Generating application key...
docker compose exec php php artisan key:generate --no-interaction

echo Running migrations...
docker compose exec php php artisan migrate --force

echo Seeding database...
docker compose exec php php artisan db:seed

echo.
echo ========================================
echo  Project is ready!
echo  App:        http://localhost:8080
echo  API:        http://localhost:8080/api/products
echo  API Docs:   http://localhost:8080/docs
echo  phpMyAdmin: http://localhost:8081
echo ========================================
echo.
echo Useful commands:
echo   docker compose exec php php artisan test        Run tests
echo   docker compose exec php php artisan migrate:fresh --seed  Reset DB
echo   docker compose exec php php artisan pint        Format code
echo   docker compose exec php bash                    Enter container
echo.
pause
