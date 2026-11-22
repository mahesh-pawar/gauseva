# Lesson 03 — Routes

**Status:** ✅ Done (2026-11-22)

**Goal:** public URLs for GauSeva (`/about`, `/cows`, `/cows/{slug}`) and an admin URL group — using closures and a hardcoded array for now. Controllers come in Lesson 4, the database in Lesson 7.

## CI4 → Laravel

| What | CI4 (`app/Config/Routes.php`) | Laravel (`routes/web.php`) |
|---|---|---|
| Basic | `$routes->get('about', 'Pages::about');` | `Route::get('/about', ...)` |
| View only | — | `Route::view('/about', 'about')` |
| Parameter | `cows/(:segment)` → `$1` | `/cows/{slug}` → `function (string $slug)` (matched **by name**) |
| Numbers only | `orders/(:num)` | `->whereNumber('id')` |
| Optional | separate route | `/products/{category?}` + default value |
| Name a route | `['as' => 'cows.show']` | `->name('cows.show')` |
| Build URL | `url_to('cows.show', 'gauri')` | `route('cows.show', 'gauri')` |
| Group | `$routes->group('admin', ...)` | `Route::prefix('admin')->name('admin.')->group(...)` |
| Redirect | `$routes->addRedirect('home', '/')` | `Route::redirect('/home', '/')` |
| Custom 404 | `set404Override()` | `Route::fallback(...)` |
| 404 from code | `throw PageNotFoundException::forPageNotFound()` | `abort(404)` |
| List routes | `php spark routes` | `php artisan route:list --except-vendor` |

## Tasks

### Task 1 — View-only route ✅
`resources/views/about.blade.php` with an `<h1>` and a sentence, then:
```php
Route::view('/about', 'about')->name('about');
```
- View name has no `.blade.php`; subfolders use dots: `resources/views/pages/about.blade.php` → `'pages.about'`.
- Same as `Route::get('/about', fn () => view('about'))` — `Route::view` is a shortcut for pages with no logic.

### Task 2 — Sample data + list ✅
```php
$cows = [
    'gauri'    => ['name' => 'Gauri',    'breed' => 'Gir',        'age' => 5],
    'kamdhenu' => ['name' => 'Kamdhenu', 'breed' => 'Sahiwal',    'age' => 7],
    'nandini'  => ['name' => 'Nandini',  'breed' => 'Tharparkar', 'age' => 3],
];

Route::get('/cows', function () use ($cows) {
    $html = '<h1>Our Cows</h1>';
    foreach ($cows as $slug => $cow) {
        $html .= '<p><a href="'.route('cows.show', $slug).'">'.$cow['name'].'</a></p>';
    }
    return $html;
})->name('cows.index');
```

### Task 3 — Route parameter ✅
```php
Route::get('/cows/{slug}', function (string $slug) use ($cows) {
    if (! isset($cows[$slug])) {
        abort(404);
    }
    $cow = $cows[$slug];
    return "<h1>{$cow['name']}</h1><p>Breed: {$cow['breed']}, Age: {$cow['age']}</p>"
        .'<a href="'.route('cows.index').'">← All cows</a>';
})->name('cows.show');
```
Mistake made & learned: `/cows/gauri` gave **500 `Route [cows.index] not defined`** because the Task 2 route didn't exist yet. `route()` fails loudly — a hardcoded `'/cows'` would have silently produced a broken link.

### Task 4 — Optional parameter ✅
```php
Route::get('/products/{category?}', function (string $category = 'all') {
    return "Showing products in: {$category}";
})->name('products.index');
```
`/products` → `all`, `/products/ghee` → `ghee`. `?` = optional, so the function parameter **must** have a default.
Experiment: remove `= 'all'` and open `/products` → error.

### Task 5 — Constraint (numbers only) ✅
```php
Route::get('/orders/{id}', function (int $id) {
    return "Order #{$id}";
})->whereNumber('id')->name('orders.show');
```
`/orders/42` → `Order #42`, `/orders/abc` → 404.
Experiment: remove `->whereNumber('id')` and open `/orders/abc` → what happens with `int $id`?

Mistake made & learned: first version was `orders/{id?}` with `int $id` and no default → `/orders` crashed with **500 "Too few arguments"**. An order page needs an id, so `{id}` must not be optional. Also renamed `orders.index` → `orders.show` (`.show` = one item, `.index` = list).

Other constraints:
```php
->whereAlpha('slug')                  // letters only
->whereAlphaNumeric('code')           // letters + digits
->whereIn('type', ['ghee', 'milk'])   // only these values
->where('slug', '[a-z0-9-]+')         // own regex — best for real slugs (have hyphens)
->where(['slug' => '[a-z0-9-]+', 'id' => '[0-9]+'])   // several at once
```

Multiple parameters (optional exercise):
```php
Route::get('/cows/{slug}/orders/{id}', function (string $slug, int $id) {
    return "Cow {$slug}, order #{$id}";
})->where('slug', '[a-z0-9-]+')->whereNumber('id');
```
Test: `/cows/gir-gauri/orders/5` ✅, `/cows/gir-gauri/orders/abc` → 404, `/cows/GIR/orders/5` → 404.

### Task 6 — Redirect ✅
```php
Route::redirect('/home', '/');
```
Open `/home` → lands on `/`. (Default is 302; `Route::permanentRedirect()` = 301.)

### Task 7 — Route group for admin ✅
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => 'Admin Dashboard')->name('dashboard');
    Route::get('/cows', fn () => 'Admin: manage cows')->name('cows');
});
```
`prefix('admin')` → URLs start with `/admin`; `name('admin.')` → names start with `admin.`.

### Task 8 — Custom 404 ✅ (must be the **last** route)
```php
Route::fallback(function () {
    return response('<h1>404 — Page not found</h1><a href="/">Go home</a>', 404);
});
```

### Task 9 — Inspect ✅
```powershell
php artisan route:list --except-vendor
vendor\bin\pint
```
Check the **Name** column: `cows.index`, `cows.show`, `admin.dashboard` …

## Questions & answers
1. **What full name did the admin dashboard route get, and why?**
   `admin.dashboard` (and `admin.cows`). The group's `->name('admin.')` is a prefix joined to each route's own name. Watch the dot: `name('admin')` would give `admindashboard`.
   `prefix('admin')` changes the **URL**, `name('admin.')` changes the **name** — two separate jobs.
2. **Why is `route('cows.show', 'gauri')` better than hardcoding `'/cows/gauri'`?**
   Change the URL in one place (`web.php`) and every link follows. Also: an unknown name throws an error immediately instead of making a silent broken link, and parameters are filled in for you.
3. **If `/cows/create` is added below `/cows/{slug}`, what happens?**
   404. Routes are matched **top to bottom, first match wins**. `{slug}` matches any text, so `create` goes to the cow-detail closure with `$slug = 'create'`, no such cow → `abort(404)`. The create route is never reached.
   Fix: put fixed URLs **above** parameter URLs (or constrain `{slug}`). Same rule in CI4.

## Final results (all tested)

| URL | Status | Shows |
|---|---|---|
| `/home` | 302 → `/` | redirect |
| `/admin/dashboard` | 200 | Admin Dashboard |
| `/admin/cows` | 200 | Cows |
| `/dashboard` | 404 | custom fallback page (no `/admin` prefix) |
| `/xyz` | 404 | custom fallback page |
| `/orders/abc` | 404 | custom fallback page — `whereNumber` means **no route matches**, so fallback runs |
| `/cows/xyz` | 404 | Laravel's **default** page — a route **did** match, our code called `abort(404)`; fallback only handles URLs matching no route. Unified via `resources/views/errors/404.blade.php` in Lesson 52 |

`route:list --except-vendor` → 11 routes, including `admin.dashboard`, `admin.cows`, `cows.index`, `cows.show`, `orders.show`, `products.index`, and `{fallbackPlaceholder}`.

## Gotcha: VS Code undid Pint's formatting
After Pint ran, the file came back with `' . route() . '` and `!isset` — VS Code's PHP formatter (Intelephense, PSR-12 rules) reformatted it. Two formatters with different rules fight each other.
Pint is the source of truth for this project → run `vendor\bin\pint` right before every commit, and avoid **Format Document** (Shift+Alt+F) on PHP files, or switch the PHP formatter to a Pint extension.

## Concepts learned

### Closures — plain PHP, not Laravel
```php
function (string $slug) use ($cows) { ... }
```
- `function () {}` — anonymous function (closure), PHP 5.3+. CI4 routes accept them too.
- `string $slug` — Laravel fills it from `{slug}` in the URL.
- `use ($cows)` — PHP closures can't see outside variables; `use` copies them in.
- `fn () => 'text'` — PHP 7.4 arrow function: one expression, auto-returns, sees outside variables automatically.

### Named routes
- `->name('cows.index')` gives the route a nickname; `route('cows.index')` builds its URL.
- Change the URL in one place, every `route()` call follows. Unknown name → immediate error.
- `route()` returns a **full URL** (`http://localhost:8000/cows/gauri`).
- Convention: `cows.index`, `cows.show`, `cows.create`, `cows.store`, `cows.edit`, `cows.update`, `cows.destroy` — exactly what `Route::resource()` creates in Lesson 4.

### Debugging
- Error page in the browser shows file + line.
- Log: `storage/logs/laravel.log` (CI4: `writable/logs/`).
