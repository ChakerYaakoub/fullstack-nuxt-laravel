# FERRA Backend (Laravel + SQLite)

## What you installed
- PHP 8.3
- Composer
- Laravel 13
- SQLite (file DB — no MySQL needed)

## Run

```powershell
cd backend
php artisan serve
```

API: http://localhost:8000/api

## Useful commands

```powershell
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
```

SQLite file: `database/database.sqlite`
