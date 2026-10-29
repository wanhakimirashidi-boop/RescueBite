# RescueBite Development Rules

## Project Approach

- Build RescueBite as a standard Laravel and MySQL application.
- Keep each change small, focused, and limited to files directly related to the requested feature.
- Explain each completed change concisely: what changed, why it is needed, and how the main variables or files connect.
- Preserve all existing user-written comments. Do not delete or rewrite them unless the user explicitly asks.
- Use clear, descriptive names for variables, methods, routes, models, migrations, tables, Blade files, and CSS classes.
- Avoid vague names such as `$data`, `test`, or `temp`.

## Laravel Structure

- Keep `routes/web.php` for route definitions, middleware, route names, and controller mappings only.
- Put `return view(...)`, queries, validation, and request handling in controllers.
- Use standard Laravel folders:
  - Controllers: `app/Http/Controllers`
  - Models: `app/Models`
  - Middleware: `app/Http/Middleware`
  - Migrations: `database/migrations` (ensure to only use normal up and down functions)
  - Seeders: `database/seeders`
  - Views: `resources/views`
  - Public CSS: `public/css`
- Use Blade layouts with `@extends`, `@section`, and `@yield` for page structure.
- Use `@include` only for meaningful repeated Blade UI or behavior. Do not create partials for a few lines used once.
- Keep Laravel Breeze authentication, its existing routes, and existing `<x-component>` behavior working unless there is a clear reason to change it.

## Code Style

- Write normal, beginner-friendly PHP, Blade, HTML, CSS, and JavaScript.
- Prefer readable `if`, `else`, `foreach`, arrays, normal Eloquent queries, and explicit controller methods.
- Do not use ternary operators in project code.
- Do not add `@php` blocks to RescueBite Blade views.
- Use standard model properties such as `protected $fillable`, `protected $hidden`, and `protected $casts`; do not use PHP attribute syntax such as `#[Fillable(...)]`.
- Use conventional Laravel relation methods with clear names, for example `vendorOutlet()`, `foodListings()`, and `reservations()`.
- Use a `Service` class when repeated business logic or a multi-step workflow appears in more than one controller or action.
- Use `Helpers.php` only for small reusable non-business utilities such as formatting. Do not create a helper or service only to move a few lines of code.
- Do not add unnecessary files, abstractions, packages, or advanced patterns.

## Database And Backend

- Implement backend and database logic when a UI feature needs real functionality; do not leave misleading frontend-only behavior such as fake pagination or fake search.
- Use singular model names and plural, lowercase snake_case table names, for example `FoodListing` and `food_listings`.
- Use migrations with `up()` to create or change schema and `down()` to reverse the exact change.
- Never edit a migration that has already run in the local database. Create a new migration for later schema changes.
- Use seeders for repeatable test users and test data.
- Validate every form request in the controller before saving data.
- Protect role-specific routes with `auth`, `verified`, and the appropriate role middleware.
- Verify a vendor owns a food listing before allowing that vendor to update or delete it.

## RescueBite Business Rules

- Roles are `consumer`, `vendor`, and `admin`.
- The system is open to the public. UNITEN is a nearby/community UI focus only; do not add UNITEN-specific backend columns, variables, priorities, or access rules unless the user explicitly requests them.
- Current design: one vendor user account manages one `vendor_outlet`.
- Revisit the data structure only if the user later requests multiple staff accounts for one outlet.
- Food listings belong to a vendor outlet; reservations belong to one consumer and one food listing.
- Uploaded food images are stored as local public-storage paths in `image_path`, not external image URLs.

## Verification

- After relevant changes, run focused checks such as PHP syntax checks, `php artisan view:cache`, route checks, migration status, and `git diff --check`.
- Do not run destructive database commands such as `migrate:fresh` unless the user explicitly requests them.
