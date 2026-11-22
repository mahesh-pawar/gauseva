# GauSeva — Laravel Learning Roadmap

54 lessons in 10 modules. Every lesson teaches one Laravel concept by building a real piece of GauSeva.
Each module ends with a short **"build it alone"** exercise to check understanding.

**Legend:** ✅ done · 🟡 in progress · ⏳ upcoming
Detailed notes for finished lessons: see the [docs index](README.md).

## What GauSeva does

1. **Shop** — ghee, cow dung products, gomutra, panchamrit (with variants: 250 ml, 500 ml, 1 kg).
2. **Daily fresh milk subscription** — subscribe to 1 L/day, pause ("away 10–15 Dec"), skip a day, change quantity; nightly job generates tomorrow's deliveries; prepaid wallet charged per delivery; delivery staff get a route list.
3. **Feed a cow (gau seva)** — cow profiles, donate for a day's fodder / a month's care / sponsor a cow, PDF receipt by email, photo updates.
4. **Admin panel** — cows, products, stock, subscriptions, deliveries, donations, reports.

---

## Module 1 — Foundations

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 1 | Connect the database | `.env`, config, migrations, batches | Laravel ↔ PostgreSQL 18 | ✅ |
| 2 | Request lifecycle | `public/index.php` → `bootstrap/app.php` → middleware → routes → response | Trace one request, `/hello`, `/up` | ✅ |
| 3 | Routes | Parameters, optional params, constraints, named routes, `route()`, groups, redirect, fallback | `/about`, `/cows/{slug}`, admin group, custom 404 | ✅ |
| 4 | Controllers | `make:controller`, resource / invokable controllers, request injection | `CowController`, `HomeController`, `Admin\CowController` | ✅ |
| 5 | Blade | Layouts (`@extends`/`@section`) vs components (`<x-layout>`), `@foreach`, `{{ }}` escaping | Site layout, navbar, footer, cow pages as real views | ⏳ |
| 6 | Frontend assets | Vite, Tailwind, `@vite` | Styled homepage | ⏳ |

## Module 2 — Database & Eloquent (the most important module)

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 7 | First migration | Column types, indexes, `migrate:rollback`, `migrate:fresh` | `cows` table | ⏳ |
| 8 | First model | Eloquent basics, `$fillable`, mass assignment, `tinker`, route model binding | `Cow` model replaces the hardcoded array | ⏳ |
| 9 | Factories & seeders | Fake data, `db:seed` | 20 sample cows | ⏳ |
| 10 | Query builder vs Eloquent | `where`, `orderBy`, `paginate` — vs CI4 `$builder` | Cow list with pagination | ⏳ |
| 11 | Relationships 1 | `hasMany` / `belongsTo` | `products` → `product_variants` (500 ml, 1 L ghee) | ⏳ |
| 12 | Relationships 2 | `belongsToMany` + pivot, polymorphic | Product categories, images | ⏳ |
| 13 | N+1 problem | Eager loading `with()`, `preventLazyLoading` | Fix a slow product page | ⏳ |
| 14 | Casts & accessors | `casts()`, enums, `Attribute` accessors, money handling | Price stored in paise, shown as ₹ | ⏳ |
| 15 | Scopes | Local scopes | `Product::active()->inStock()` | ⏳ |
| 16 | PostgreSQL extras | JSONB columns, `->where('meta->x')`, GIN indexes | Product attributes as JSONB | ⏳ |

## Module 3 — Forms, validation & CRUD

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 17 | Forms & CSRF | `@csrf`, `@method`, `old()`, flash messages | Admin "add cow" form | ⏳ |
| 18 | Validation | `$request->validate()` → **Form Requests**, custom rules, `@error` | `StoreCowRequest` | ⏳ |
| 19 | Full resource CRUD | Route model binding, `Route::resource` end to end | Admin CRUD for cows & products | ⏳ |
| 20 | File uploads | `Storage`, disks, `storage:link` | Cow & product photos | ⏳ |
| 21 | Soft deletes | `SoftDeletes`, restore | Archived products | ⏳ |

**Build it alone:** admin CRUD for product categories.

## Module 4 — Authentication & authorization

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 22 | Authentication | Starter kit, `Auth` facade, `auth` middleware, sessions | Register / login | ⏳ |
| 23 | Middleware | Writing middleware (= CI4 Filters), route groups | `EnsureUserIsAdmin` on `/admin` | ⏳ |
| 24 | Roles | Enum role column | customer / admin / delivery | ⏳ |
| 25 | Gates & policies | `can()`, `@can`, `authorize()` | Only admins edit cows; customers see only their own orders | ⏳ |

## Module 5 — Shop

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 26 | Sessions | Session storage | Cart | ⏳ |
| 27 | Service classes | Logic out of controllers, dependency injection | `CartService`, `OrderService` | ⏳ |
| 28 | Transactions & row locks | `DB::transaction`, `lockForUpdate()` | Safe checkout — no overselling | ⏳ |
| 29 | Events & listeners | `OrderPlaced` → listeners | Reduce stock, notify admin | ⏳ |
| 30 | Payments | HTTP client, webhooks, signature check, idempotency | Razorpay **test mode** | ⏳ |

**Build it alone:** coupon codes at checkout.

## Module 6 — Milk subscriptions (advanced core)

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 31 | Modelling subscriptions | State logic, enums, Carbon dates | Subscribe to 1 L daily | ⏳ |
| 32 | Pause & skip | PostgreSQL `daterange` columns | "Away 10–15 Dec" | ⏳ |
| 33 | Artisan commands | `make:command` | `deliveries:generate` | ⏳ |
| 34 | Task scheduler | Schedules in `routes/console.php` | Run nightly at 9 pm | ⏳ |
| 35 | Wallet ledger | Append-only transactions, `CHECK` constraint | Prepaid wallet | ⏳ |
| 36 | Delivery panel | Scoped queries, policies | Today's route list for delivery staff | ⏳ |

**Build it alone:** weekly subscription (e.g. ghee every Sunday).

## Module 7 — Background work & communication

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 37 | Queues & jobs | `make:job`, `queue:work`, retries, failed jobs | Background delivery processing | ⏳ |
| 38 | Mail | Mailables, Markdown mail, Mailpit | Order confirmation email | ⏳ |
| 39 | Notifications | Mail + database channels | Low wallet balance alert | ⏳ |
| 40 | PDF | Generate PDF in a queued job | Donation receipt | ⏳ |
| 41 | Feed a cow | Combining all of the above | Donation flow | ⏳ |

## Module 8 — API

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 42 | API routes | `routes/api.php`, JSON, `Route::apiResource` | Delivery staff API | ⏳ |
| 43 | Sanctum | Token authentication | Mobile app login | ⏳ |
| 44 | API Resources | Shaping JSON output | `DeliveryResource` | ⏳ |
| 45 | Rate limiting | `RateLimiter` | Protect the API | ⏳ |

## Module 9 — Quality & framework internals

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 46 | Testing | Pest feature tests, `RefreshDatabase`, factories in tests | Checkout & subscription tests | ⏳ |
| 47 | Service container & providers | Binding, singletons, facades vs injection | Swappable payment gateway | ⏳ |
| 48 | Collections | `map`, `filter`, `groupBy`, `sum` | Reports | ⏳ |
| 49 | Cache | `Cache::remember`, invalidation | Fast homepage | ⏳ |
| 50 | Observers | Model lifecycle events | Audit log | ⏳ |
| 51 | Reports | Window functions, raw expressions | Monthly milk & donation reports | ⏳ |
| 52 | Errors & logging | Exception handling, `errors/404.blade.php`, logs | Production-ready error pages | ⏳ |

## Module 10 — Modern frontend & deployment

| # | Lesson | Laravel concept | What you build | Status |
|---|---|---|---|---|
| 53 | Livewire | Reactive components without writing JS | Pause / skip calendar | ⏳ |
| 54 | Deployment | `config:cache`, `optimize`, queue worker, cron | Go live | ⏳ |

---

## Core tables (by the end)

`users`, `cows`, `cow_updates`, `products`, `product_variants`, `orders`, `order_items`, `payments`, `subscriptions`, `subscription_pauses`, `deliveries`, `wallets`, `wallet_transactions`, `donations`

## How each lesson works

1. **Concept** — short explanation + CI4 comparison.
2. **Tasks** — written in `docs/lesson-NN-*.md` at the **start** of the lesson; you type the code.
3. **Check** — I review files and test every URL; mistakes are noted in the lesson doc.
4. **Before commit** — `vendor\bin\pint`, `git diff` (no `dd(`).
5. **Push** — only when you say so; commits under your name only.
