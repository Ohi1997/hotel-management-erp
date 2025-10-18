# Hotel Management ERP Backoffice

Laravel 12 + Livewire 3 implementation of a hotel backoffice. This project provides a modern admin for managing customers, bookings, room inventory, wake-up calls, and payments with role-based access powered by **spatie/laravel-permission**.

## Tech stack

- PHP 8.3+
- Laravel 12 (Breeze / Blade)
- Livewire 3 + Alpine.js
- Tailwind CSS
- MySQL 8
- Spatie Laravel Permission

## Getting started

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build   # or `npm run dev` while developing
```

### Default credentials

| Role    | Email                 | Password |
|---------|-----------------------|----------|
| Admin   | `admin@example.com`   | `password` |
| Manager | `manager@example.com` | `password` |
| Cashier | `cashier@example.com` | `password` |

These accounts are created by `RolesAndUsersSeeder`. Running `php artisan migrate --seed` will populate demo rooms, customers, bookings, payments, and wake-up calls for exploration.

## Backoffice modules

| Module      | Features | Permissions |
|-------------|----------|-------------|
| Customers   | Search, create/edit via Livewire modals, detail drawer with stay history. | admin, manager (manage), cashier (view) |
| Bookings    | Reservation list with filters, modal-based CRUD, check-in/out workflows, balance tracking. | admin, manager |
| Payments    | Payment ledger and capture modal that updates booking balances in real time. | admin, cashier |
| Rooms       | Status board (availability & housekeeping), room type editor, floor manager. | admin, manager (board), admin (setup) |
| Wake-ups    | Schedule/manage wake-up calls with status automation. | admin, manager, cashier |

Each Livewire component supports Alpine powered modals (`x-show`/`x-cloak`) with unique `wire:key` values to ensure correct hydration when inserted dynamically. Livewire events (`dispatch('customer-saved')`, etc.) are used to refresh lists, emit toasts, and close modals without page reloads.

## Architecture notes

- **Policies:** Access control is enforced by policies registered in `AppServiceProvider`. Spatie roles gate each module.
- **Observers:** An `AuditableObserver` logs create/update/delete events to the `audit_logs` table for the primary entities.
- **Events:** `BookingConfirmed` and `PaymentRecorded` domain events are dispatched on status transitions and new payments.
- **Factories & seeders:** Comprehensive factories plus `BackofficeSampleSeeder` provide demo data for local development.
- **UI layout:** `resources/views/layouts/backoffice.blade.php` delivers a sidebar layout, toast notifications (Livewire + Alpine), and shared asset includes.

## Testing

Run the full PHPUnit test suite:

```bash
php artisan test
```

Ensure database migrations are up to date before executing tests.

> **Note:** GitHub rate limiting may cause `composer install` to request an OAuth
> token when fetching Livewire/Pest packages. Generate a temporary token at
> https://github.com/settings/tokens/new if installation fails with a 403.

## Module overview

- **Customers:** `App\Livewire\Backoffice\Customers\*` + `resources/views/livewire/backoffice/customers`.
- **Bookings:** `App\Livewire\Backoffice\Bookings\*` + `resources/views/livewire/backoffice/bookings`.
- **Payments:** `App\Livewire\Backoffice\Payments\*` + `resources/views/livewire/backoffice/payments`.
- **Rooms:** `App\Livewire\Backoffice\Rooms\*` + `resources/views/livewire/backoffice/rooms`.
- **Wake-ups:** `App\Livewire\Backoffice\WakeUps\*` + `resources/views/livewire/backoffice/wake-ups`.

Refer to `routes/backoffice.php` for the full route map grouped under the `/backoffice` prefix.
