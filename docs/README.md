# GauSeva — Learning Notes

Learning Laravel (coming from CodeIgniter 3/4) by building **GauSeva**: a gaushala site to buy fresh cow milk, ghee, cow dung, gomutra and panchamrit, subscribe to daily milk delivery, and donate to feed cows.

**Stack:** Laravel 13 · PHP 8.4 · PostgreSQL 18 · Windows

## Quick start (daily use)

```powershell
cd "C:\Users\HP\My Learning Projects\gauseva"
php artisan serve        # http://localhost:8000
```

New machine? Start with [00 — Environment Setup](00-environment-setup.md).
Full plan — all 54 lessons with concept + what you build: **[ROADMAP.md](ROADMAP.md)**.

## Notes

| # | Note | Status |
|---|---|---|
| 00 | [Environment setup (PHP 8.4, PATH, PostgreSQL)](00-environment-setup.md) | ✅ |
| 01 | [Create project & connect PostgreSQL](lesson-01-project-and-database.md) | ✅ |
| 02 | [Request lifecycle](lesson-02-request-lifecycle.md) | ✅ |
| 03 | [Routes](lesson-03-routes.md) | ✅ |
| 04 | [Controllers](lesson-04-controllers.md) | ✅ |
| 05 | Blade layouts & components | ⏳ next |

## Curriculum (short — details in [ROADMAP.md](ROADMAP.md))

**M1 Foundations:** 1 DB & migrations ✅ · 2 Request lifecycle ✅ · 3 Routes ✅ · 4 Controllers ✅ · 5 Blade layouts & components · 6 Vite & Tailwind
**M2 Eloquent:** 7 Migrations · 8 Models · 9 Factories & seeders · 10 Query builder vs Eloquent · 11 hasMany/belongsTo · 12 belongsToMany & polymorphic · 13 N+1 & eager loading · 14 Casts, enums, accessors · 15 Scopes · 16 PostgreSQL JSONB
**M3 Forms:** 17 Forms & CSRF · 18 Validation & Form Requests · 19 Resource CRUD · 20 File uploads · 21 Soft deletes
**M4 Auth:** 22 Authentication · 23 Middleware · 24 Roles · 25 Gates & policies
**M5 Shop:** 26 Session cart · 27 Service classes · 28 Transactions & row locks · 29 Events & listeners · 30 Payments (Razorpay test)
**M6 Subscriptions:** 31 Subscription model · 32 Pause/skip (daterange) · 33 Artisan commands · 34 Scheduler · 35 Wallet ledger · 36 Delivery panel
**M7 Background:** 37 Queues & jobs · 38 Mail · 39 Notifications · 40 PDF receipts · 41 Feed-a-cow donations
**M8 API:** 42 API routes · 43 Sanctum · 44 API Resources · 45 Rate limiting
**M9 Quality:** 46 Pest tests · 47 Service container · 48 Collections · 49 Cache · 50 Observers · 51 Reports · 52 Errors & logging
**M10:** 53 Livewire · 54 Deployment
