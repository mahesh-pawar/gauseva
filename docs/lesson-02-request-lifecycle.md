# Lesson 02 — Request Lifecycle

**Status:** ✅ Done (2026-11-19)

## Commands executed

```powershell
php artisan serve          # http://localhost:8000 (Ctrl+C to stop)
php artisan route:list     # every registered route — showed 5
```

## The flow of one request

```
Browser: GET http://localhost:8000/
   │
   ▼
public/index.php          ← single entry point (same as CI4)
   │  require vendor/autoload.php   (Composer autoloader)
   │  $app = require bootstrap/app.php
   │  $app->handleRequest(Request::capture());   ← hands the request to Laravel
   ▼
bootstrap/app.php         ← builds the Application: routing, middleware, exceptions
   ▼
Middleware                ← sessions, cookies, CSRF… (CI4: Filters)
   ▼
routes/web.php            ← "/" matched → closure returns view('welcome')
   ▼
resources/views/welcome.blade.php
   ▼
Response goes back out through middleware → browser
```

### `$app->handleRequest(Request::capture());`

| Part | Does | CI4 |
|---|---|---|
| `Request::capture()` | Wraps `$_GET`, `$_POST`, `$_SERVER`, cookies, files, headers into one `Request` object | `$this->request` |
| `$app` | The Application = Laravel's **service container** | `Config\Services` + `CodeIgniter` class |
| `handleRequest()` | Middleware → router → route → sends response | `$app->run()` |

## `bootstrap/app.php`

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',                       // registers the /up route
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // register / alias middleware here
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // error handling
    })->create();
```

No `app/Config/*.php` classes like CI4: settings live in `config/*.php` (plain arrays), wiring lives here.

## What I built

`routes/web.php`

```php
Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return 'Jai Gau Mata! Laravel is working.';   // a route can return a plain string
});
```

`resources/views/welcome.blade.php` — heading changed to `Welcome to GauSeva` (Blade changes show on refresh, no restart).

## Experiments & results

| URL | Result | Why |
|---|---|---|
| `/` | Welcome page | `routes/web.php` → `view('welcome')` |
| `/hello` | Plain text | Closure returns a string |
| `/up` | **200** | Health route from `bootstrap/app.php` (`health: '/up'`) |
| `/xyz` | **404** page | No matching route |

`route:list` showed 5 routes: `/`, `hello`, `up`, `storage/{path}` (serves local files), plus the HEAD variants are included with GET.

## Concepts learned

- **`/up` health check** — returns 200 if the app boots, 500 if broken. Used by hosting (Render "Health Check Path"), load balancers, uptime monitors. Does **not** check the database by default.
- **`dd()`** = dump and die (like CI4 `dd()`/Kint or `print_r(); exit;`). `dd(request())` shows URL, method, headers, input. Always remove before committing.
- **Middleware = CI4 Filters**

| CI4 | Laravel |
|---|---|
| `app/Filters/AuthFilter.php` | `app/Http/Middleware/EnsureUserIsAdmin.php` |
| `before()` / `after()` | one `handle($request, $next)` — code before `$next()` = before, after = after |
| `app/Config/Filters.php` (`$aliases`, `$globals`) | `bootstrap/app.php` → `withMiddleware()` |
| `['filter' => 'auth']` | `->middleware('auth')` |

- **Not to confuse:** CI4 `Config/Config.php` ≈ Laravel `config/*.php`; CI4 `Config/Autoload.php` ≈ Composer `vendor/autoload.php` + `composer.json` `"autoload"`.

## Useful commands

| Command | What it does |
|---|---|
| `php artisan serve` | Dev server (`--port=8080` if 8000 busy) |
| `php artisan route:list` | All routes (CI4: `php spark routes`) |
| `vendor\bin\pint --test` | Report code-style problems (Laravel's formatter, PSR-12 based) |
| `vendor\bin\pint` | Fix them automatically — run before every commit |
