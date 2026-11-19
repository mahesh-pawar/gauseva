# Lesson 01 — Create the Project & Connect PostgreSQL

**Status:** ✅ Done (2026-11-19)

## Commands executed

```powershell
# 1. Create the project (from the parent folder)
cd "C:\Users\HP\My Learning Projects"
composer create-project laravel/laravel gauseva

# 2. Check the app boots
cd gauseva
php artisan --version          # Laravel Framework 13.43.0
php artisan about              # app name, PHP version, drivers

# 3. Create the database (done manually in pgAdmin: Databases → Create → gauseva)
#    CLI alternative:
& "C:\Program Files\PostgreSQL\18\bin\createdb.exe" -U postgres -h 127.0.0.1 gauseva

# 4. Test the connection, then migrate
php artisan db:show            # shows connection + table count
php artisan migrate
php artisan migrate:status     # all 3 migrations: Batch [1] Ran
php artisan db:show --counts   # 9 tables
```

## What `composer create-project` did automatically

- Downloaded the Laravel skeleton + all packages into `vendor/` (like the CI4 appstarter).
- Copied `.env.example` → `.env`.
- `php artisan key:generate` → set `APP_KEY` (used for encryption/sessions; CI4 equivalent: `encryption.key`).
- Ran migrations on a temporary SQLite file `database/database.sqlite` (default DB). Not used anymore.

## `.env` changes (SQLite → PostgreSQL)

```env
APP_NAME=GauSeva

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gauseva
DB_USERNAME=postgres
DB_PASSWORD=          # your password — never commit it
```

Same values (without the password) also go in `.env.example`, which **is** committed.
`.env` is in `.gitignore` → it never reaches GitHub.

## Concepts learned

**Config flow:** `.env` → read by `env('DB_HOST')` inside `config/database.php` (`'pgsql'` section) → used by the app via `config('database...')`.
CI4 equivalent: `.env` + `app/Config/Database.php`.

**Migration** = a PHP class describing a *database change*. One migration file can create several tables.

| Migration file | Tables it creates |
|---|---|
| `0001_01_01_000000_create_users_table` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001_create_cache_table` | `cache`, `cache_locks` |
| `0001_01_01_000002_create_jobs_table` | `jobs`, `job_batches`, `failed_jobs` |
| *(Laravel itself)* | `migrations` — which migrations already ran |

- `users` — login accounts (hashed password, `remember_token`)
- `password_reset_tokens` — "forgot password" tokens
- `sessions` — session storage because `SESSION_DRIVER=database`

**Batch:** every `php artisan migrate` run gets one batch number. `migrate:rollback` undoes only the **last batch** (same idea as CI4).

## Useful commands

| Command | What it does |
|---|---|
| `php artisan migrate` | Run new migrations |
| `php artisan migrate:status` | Which migrations ran, and in which batch |
| `php artisan migrate:rollback` | Undo the last batch |
| `php artisan migrate:fresh` | Drop **all** tables and re-run every migration (dev only!) |
| `php artisan db:show` | Test DB connection, list tables (`--counts` for row counts) |
| `php artisan db:table users` | Show a table's columns and indexes |
| `php artisan serve` | Dev server at http://localhost:8000 |

## CI4 → Laravel folder map

| CI4 | Laravel |
|---|---|
| `app/Controllers` | `app/Http/Controllers` |
| `app/Models` | `app/Models` (Eloquent) |
| `app/Views` | `resources/views` (Blade `.blade.php`) |
| `app/Config/Routes.php` | `routes/web.php` (+ `routes/api.php`) |
| `app/Database/Migrations` | `database/migrations` |
| `php spark` | `php artisan` |
| `public/index.php` | `public/index.php` |
