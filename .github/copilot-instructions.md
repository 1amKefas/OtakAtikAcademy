# Copilot / AI Agent Instructions for OtakAtik

This file gives concise, repo-specific guidance so an AI coding agent can be immediately productive. Focus on the concrete patterns and commands found in this codebase.

- Project type: Laravel 11/12 PHP application with Vue 3 + Vite frontend.
- DB: Oracle (yajra/laravel-oci8). Oracle Instant Client + OCI8 extension required locally or in CI to run migrations and tests that touch DB.
- Payments: Midtrans integration present (`app/Services/MidtransService.php` and `app/Http/Controllers/PaymentController.php`). Payment notifications are delivered to `POST /checkout/notification`.
- Concurrency & dev runner: `composer dev` runs multiple processes using `npx concurrently` (server, queue listener, pail logger, vite dev server).

Quick entry commands (Windows PowerShell):
```
# install php deps
composer install
# copy env and generate key
cp .env.example .env; php artisan key:generate
# install node deps and run dev
npm install; npm run dev
# run migrations (requires Oracle)
php artisan migrate
# run full dev script (runs server, queue, pail, vite concurrently)
composer dev
# run tests
composer test
```

Key files to open when changing behavior:
- `routes/web.php` — primary HTTP route organization (public, auth, admin, instructor, student groups).
- `bootstrap/app.php` and `app/Http/Kernel.php` — middleware aliases and web/api bootstrap. Custom middleware aliases: `admin`, `instructor`.
- `app/Services/MidtransService.php` — payment gateway integration logic and signature handling.
- `app/Http/Controllers/*` — controllers for auth, payment, admin, instructor, student flows.
- `app/Models/*` — Eloquent models and relations.
- `database/migrations/*` — DB schema and field names (useful for building queries/tests).
- `composer.json` and `package.json` — scripts, dev tooling, and dependency hints.
- `.github/agents/bb.agent.md` — minimal agent doc exists; keep it and do not remove.

Architectural notes and patterns (use these as assumptions unless you discover otherwise):
- Monolithic Laravel app: business logic lives in `app/Services` and controllers; data model in `app/Models`.
- Routes grouped in `routes/web.php` by role; most route protection uses middleware (`auth`, `admin`, `instructor`). Follow those groupings when adding new endpoints.
- Background work: the dev script starts `php artisan queue:listen` — queued jobs are expected. When changing background tasks, update queue worker behavior and ensure jobs are idempotent.
- External webhooks: `checkout/notification` is an ingress point for Midtrans webhooks — do not change URL or HTTP method without checking `app/Services/MidtransService` and `PaymentController`.
- Frontend: Vue 3 + Vite; assets loaded via Laravel Vite plugin. Use `npm run dev` for live reload and `npm run build` for production.

Testing and CI implications:
- Unit/feature tests use PHPUnit; `composer test` runs `php artisan test`.
- Some tests/migrations may require Oracle or DB mocking. Prefer to stub DB calls or use in-memory approaches when writing CI-friendly tests unless CI provides Oracle.

Repo-specific conventions and examples
- Middleware aliasing is declared in `bootstrap/app.php` and `app/Http/Kernel.php`. Example: `Route::middleware(['auth','admin'])->prefix('admin')->group(...)`.
- Controller methods follow expressive names: `DashboardController@index`, `PaymentController@processPayment`, `InstructorController@storeAssignment`.
- Route names: many routes are named (`->name('checkout.process')`) — use route names for links/redirections when possible.
- Database access uses Eloquent models in `app/Models`. Refer to `Course`, `CourseRegistration`, `QuizSubmission` model names when writing queries.

Safety & scope for the agent
- Do not change payment webhook URL or payload handling without explicit verification and tests.
- Avoid changing global middleware order unless necessary; instead add route-level middleware.
- When editing migrations, preserve migration ordering and avoid changing applied migrations in a way that would break existing production DBs.

If you need more context, open these concrete files first:
- `routes/web.php`
- `bootstrap/app.php`
- `app/Services/MidtransService.php`
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Middleware/AdminMiddleware.php` and `InstructorMiddleware.php`
- `composer.json`, `package.json`

Project status & roadmap:
- See `IMPLEMENTATION_STATUS.md` in project root for detailed feature checklist (SUDAH/BELUM)
- High-priority incomplete features: Quiz implementation (controller + UI), Assignment submissions, Forum diskusi
- DB migrations are ready; focus on Controller + Route + View layers

If anything here is unclear or you'd like me to expand a section (for example: typical request/response shapes for payment webhooks, or a short checklist for making schema changes), tell me which section and I'll iterate.
