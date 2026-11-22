# Lesson 04 — Controllers

**Status:** 🟡 Tasks 1–7 ✅ — questions pending (started 2026-11-22)

## What I built

| File | Purpose |
|---|---|
| `app/Http/Controllers/CowController.php` | Public `index` / `show` (data in a `private array $cows` property for now) |
| `app/Http/Controllers/HomeController.php` | Invokable — `/` returns the welcome view |
| `app/Http/Controllers/Admin/CowController.php` | Resource controller — 7 CRUD methods returning placeholder text |
| `routes/web.php` | No logic left for cows — `Route::resource('cows', CowController::class)->only(['index', 'show'])` + admin `Route::resource('cows', AdminCowController::class)` |

Tested: `/cows` 200, `/cows/gauri` 200, `/cows/gauri?lang=hi` → `Language: hi`, `/cows/xyz` 404, `/admin/cows` + `/create` + `/5` + `/5/edit` 200.

**Mistakes made & learned**
- `Route::resource('cow', ...)` (singular) → URLs `/cow`, names `cow.*` → `/cows` 404 and `/cow` **500 `Route [cows.index] not defined`**. Resource names are **plural** (`cows`): the first argument sets both URL and route names.
- Commented-out old routes deleted — git already keeps history.
- Code smell: `$cow = $this->cows[$cow];` reuses one variable for a string then an array — prefer a new name (`$data`). Disappears in Lesson 8 when `$cow` becomes a model.
- Pint `ordered_imports`: `use` lines must be alphabetical — Pint sorts them.

**Goal:** move the closures out of `routes/web.php` into controllers, then let `Route::resource` generate CRUD routes for the admin panel. `web.php` becomes a clean list of URLs → controller methods (the structure you know from CI4).

## CI4 → Laravel

| What | CI4 | Laravel |
|---|---|---|
| Folder | `app/Controllers` | `app/Http/Controllers` |
| Create | `php spark make:controller Cows` | `php artisan make:controller CowController` |
| Base class | `extends BaseController` (has `initController`, helpers, `$this->request`) | `extends Controller` — an **empty** abstract class |
| Route to method | `$routes->get('cows', 'Cows::index')` | `Route::get('/cows', [CowController::class, 'index'])` |
| Request object | `$this->request` | **injected**: `public function show(Request $request, ...)` |
| URL params | `public function show($slug)` (by position) | `public function show(string $slug)` (also by position — injected classes like `Request` are skipped; names matter for model binding, Lesson 8) |
| CRUD routes | `$routes->resource('cows')` | `Route::resource('cows', CowController::class)` |
| Subfolder | `app/Controllers/Admin/Cows.php` | `app/Http/Controllers/Admin/CowController.php` |
| Naming | `Cows` (plural) | `CowController` (singular + `Controller`) |

`[CowController::class, 'index']` — `CowController::class` is plain PHP: it gives the full class name as a string (`'App\Http\Controllers\CowController'`). Using `::class` instead of a string means your editor and PHP catch typos.

---

## Task 1 — Generate your first controller

```powershell
php artisan make:controller CowController
```

Open `app/Http/Controllers/CowController.php` and look at it: a namespace, a class extending `Controller`, nothing else. Artisan just saved you from typing boilerplate (CI4's `spark make:controller` does the same).

## Task 2 — Move the cow closures into the controller

In `CowController.php`, add the data as a property and two methods:

```php
class CowController extends Controller
{
    private array $cows = [
        'gauri' => ['name' => 'Gauri', 'breed' => 'Gir', 'age' => 5],
        'kamdhenu' => ['name' => 'Kamdhenu', 'breed' => 'Sahiwal', 'age' => 7],
        'nandini' => ['name' => 'Nandini', 'breed' => 'Tharparkar', 'age' => 3],
    ];

    public function index()
    {
        $html = '<h1>Our Cows</h1>';
        foreach ($this->cows as $slug => $cow) {
            $html .= '<p><a href="'.route('cows.show', $slug).'">'.$cow['name'].'</a></p>';
        }

        return $html;
    }

    public function show(string $slug)
    {
        if (! isset($this->cows[$slug])) {
            abort(404);
        }
        $cow = $this->cows[$slug];

        return "<h1>{$cow['name']}</h1><p>Breed: {$cow['breed']}, Age: {$cow['age']}</p>"
            .'<a href="'.route('cows.index').'">← All cows</a>';
    }
}
```

Notice: no more `use ($cows)` — the data is a class property, reached with `$this->cows`.
(This array is temporary — in Lesson 8 it becomes a `Cow` model reading PostgreSQL.)

Then in `routes/web.php`:
1. Add at the top, under the existing `use` line:
   ```php
   use App\Http\Controllers\CowController;
   ```
2. Replace the two cow closures with:
   ```php
   Route::get('/cows', [CowController::class, 'index'])->name('cows.index');
   Route::get('/cows/{slug}', [CowController::class, 'show'])->name('cows.show');
   ```
3. Delete the `$cows = [...]` array at the top of `web.php` — nothing uses it now.

**Test:** `/cows`, `/cows/gauri`, `/cows/xyz` must behave exactly as before (200, 200, 404). Same behaviour, cleaner structure — that's a refactor.

## Task 3 — Inject the Request object

Change `show` to receive the request:

```php
use Illuminate\Http\Request;   // at the top of the controller file

public function show(Request $request, string $slug)
{
    // ... existing code ...
    $lang = $request->query('lang', 'en');   // ?lang=hi  → 'hi', missing → 'en'
    // add {$lang} somewhere in the returned HTML, e.g. "<p>Language: {$lang}</p>"
}
```

**Test:** `/cows/gauri` → `Language: en`, `/cows/gauri?lang=hi` → `Language: hi`.

You never created `$request` — Laravel saw the `Request` **type hint** and passed the current request in. This is **dependency injection**; Laravel's service container does it for controller methods and constructors. CI4 equivalent: `$this->request->getGet('lang')`.

`$request->query('lang')` = only the query string. Also know: `$request->input('name')` (query **or** POST body), `$request->all()`.

## Task 4 — A single-action (invokable) controller for the home page

Some controllers do exactly one thing. Generate one:

```powershell
php artisan make:controller HomeController --invokable
```

It has a single `__invoke()` method. Make it return the welcome view:

```php
public function __invoke()
{
    return view('welcome');
}
```

In `web.php` (add the `use App\Http\Controllers\HomeController;` line), replace the `/` closure with:

```php
Route::get('/', HomeController::class)->name('home');
```

No method name — Laravel calls `__invoke()` automatically.
**Test:** `/` still shows the welcome page.

## Task 5 — Resource controller for the admin panel

Generate a controller in a subfolder, with all 7 CRUD methods pre-written:

```powershell
php artisan make:controller Admin/CowController --resource
```

Open `app/Http/Controllers/Admin/CowController.php` — see `index, create, store, show, edit, update, destroy` with empty bodies. Make each return a short text so you can see which one runs, e.g.:

```php
public function index()   { return 'Admin: list cows'; }
public function create()  { return 'Admin: form to add a cow'; }
public function store(Request $request) { return 'Admin: save new cow'; }
public function show(string $id)  { return "Admin: show cow {$id}"; }
public function edit(string $id)  { return "Admin: edit form for cow {$id}"; }
public function update(Request $request, string $id) { return "Admin: update cow {$id}"; }
public function destroy(string $id) { return "Admin: delete cow {$id}"; }
```

In `web.php`, two controllers are now called `CowController` (public and admin). Import the admin one with an **alias**:

```php
use App\Http\Controllers\Admin\CowController as AdminCowController;
```

Inside your admin group, **replace** the `Route::get('/cows', fn () => 'Cows')->name('cows');` line with:

```php
Route::resource('cows', AdminCowController::class);
```

Then run:

```powershell
php artisan route:list --except-vendor --path=admin
```

**Expected:** 7 new routes from one line:

| Method | URL | Name | Controller method |
|---|---|---|---|
| GET | `/admin/cows` | `admin.cows.index` | index |
| GET | `/admin/cows/create` | `admin.cows.create` | create |
| POST | `/admin/cows` | `admin.cows.store` | store |
| GET | `/admin/cows/{cow}` | `admin.cows.show` | show |
| GET | `/admin/cows/{cow}/edit` | `admin.cows.edit` | edit |
| PUT/PATCH | `/admin/cows/{cow}` | `admin.cows.update` | update |
| DELETE | `/admin/cows/{cow}` | `admin.cows.destroy` | destroy |

**Test in the browser (GET only):** `/admin/cows`, `/admin/cows/create`, `/admin/cows/5`, `/admin/cows/5/edit`.
POST/PUT/DELETE need a form — Lesson 17.

Notice `/admin/cows/create` is listed **before** `/admin/cows/{cow}` — Laravel orders resource routes correctly, avoiding the "first match wins" trap from Lesson 3.

## Task 6 — Limit resource routes

The public side only needs `index` and `show`. Replace your two public cow routes from Task 2 with:

```php
Route::resource('cows', CowController::class)->only(['index', 'show']);
```

Run `route:list` again — still named `cows.index` and `cows.show`.
⚠️ The URL parameter is now `{cow}`, not `{slug}`. Rename the method parameter in `CowController::show` from `$slug` to `$cow` (and update its uses) so URL and code use the same name. Test `/cows/gauri` again.

> **Correction (found during this lesson):** route parameters are passed to the method **in order**, not by name. Proof: `Admin\CowController::show(string $id)` works with URL `{cow}`. Class type-hints like `Request` are injected and don't use up a position. So `$slug` would still *work* — but keep the names identical anyway: (1) readability, (2) **route model binding** (Lesson 8) *does* use the name — `{cow}` only becomes a `Cow` model if the method parameter is `Cow $cow`.

(Opposite of `only`: `->except(['destroy'])`. For JSON APIs: `Route::apiResource()` — Lesson 42.)

## Task 7 — Tidy up and inspect

```powershell
php artisan route:list --except-vendor
vendor\bin\pint
git diff
```

`web.php` should now contain **no cow logic** — only route definitions.

---

## Questions (answer at the end)

1. In Task 3, you never wrote `new Request()` — how did `$request` get a value?
2. `Admin\CowController::show(string $id)` works even though the URL says `{cow}`. What does that tell you about how Laravel passes route parameters? Why rename to `$cow` anyway?
3. `Route::resource('cows', ...)` created a route for `create` and `edit`. Why would a JSON API (mobile app) **not** need those two? (Hint: what do they return?)
4. CI4 controllers extend `BaseController`, which loads helpers and gives `$this->request`. Laravel's base `Controller` is empty — where do those things come from instead?

## Useful commands

| Command | What it does |
|---|---|
| `php artisan make:controller NameController` | Empty controller |
| `... --invokable` | Single `__invoke()` method |
| `... --resource` | All 7 CRUD methods |
| `... Admin/NameController` | In a subfolder + namespace `App\Http\Controllers\Admin` |
| `php artisan route:list --path=admin` | Only routes whose URL starts with `admin` |
