# Hotel Management ERP Backoffice

A Laravel 12 + Livewire 3 powered backoffice for managing customers, bookings, payments, rooms, and wake-up requests for the hotel-management-erp stack. The system uses Breeze (Blade), Alpine.js, Tailwind CSS, and spatie/laravel-permission for role-aware navigation and authorization.

## Tech Stack

- PHP 8.3+, Laravel 12, MySQL 8
- Livewire 3 + Alpine.js, Tailwind CSS
- Breeze (Blade stack)
- spatie/laravel-permission

## Local Setup

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment**
   - Copy `.env.example` to `.env` and update database/mail credentials.
   - Ensure `DB_CONNECTION=mysql` and create the application database (e.g. `hotel-management`).

3. **Database**
   ```bash
   php artisan migrate --seed
   ```
   This seeds core roles (`admin`, `manager`, `cashier`), demo users, and sample operational data for rooms, bookings, payments, and wake-up calls.

4. **Test database**
   - Create a dedicated testing database (defaults to `hotel_management_test`).
   - No SQLite driver is required; tests run against MySQL using the credentials defined in `phpunit.xml`.

5. **Run the backoffice**
   ```bash
   php artisan serve
   npm run dev
   ```
   Login with any seeded user (e.g. `admin@example.com` / `password`).

6. **Execute the test suite**
   ```bash
   php artisan test
   ```

## Module Overview

| Module | Livewire Components | Highlights |
| --- | --- | --- |
| Customers | `Backoffice\Customers\Index`, `Form`, `Show` | Modal-based CRUD with Alpine-powered, keyed Livewire forms, customer metrics, and detail drawer. |
| Bookings | `Backoffice\Bookings\Index`, `Form`, `CheckIn`, `CheckOut` | Reservation workflow, status transitions updating room availability, eager-loaded room/customer data, dispatches `BookingConfirmed`. |
| Payments | `Backoffice\Payments\Index`, `AddPayment` | Records payments per booking/customer, auto-assigns current user (`recorded_by`), dispatches `PaymentRecorded`. |
| Rooms | `Backoffice\Rooms\StatusBoard`, `RoomType`, `Floor` | Status dashboard with filters, room type & floor management, audit logging on every mutation. |
| Wake-Ups | `Backoffice\WakeUps\Index`, `Form` | Schedule and track reminders per booking with status lifecycle. |

## Architecture Notes

- **Observers & Audit Trail**: `AuditableObserver` records create/update/delete events to the `audit_logs` table for customers, bookings, payments, rooms, and wake-up calls.
- **Events**: `BookingConfirmed` and `PaymentRecorded` fire dedicated listeners for logging and future integrations.
- **Authorization**: Policies cover all operational models; routes are grouped under `backoffice` with `auth`, `verified`, and role middleware.
- **UI Layout**: `layouts/backoffice.blade.php` hosts the shared navigation, role-aware sidebar, Alpine toast notifications (`$dispatch('toast', ...)`), and Livewire assets.
- **Factories & Seeders**: Comprehensive factories (`Floor`, `RoomType`, `Room`, `Booking`, `Payment`, `WakeUp`, `Customer`) plus `DatabaseSeeder` populates realistic demo data.

## Testing & QA

- Feature coverage uses Livewire component tests in `tests/Feature/Backoffice/LivewireCrudTest.php` to assert end-to-end CRUD flows.
- Breeze feature tests remain enabled and run against MySQL.
- Run `php artisan test` after modifying migrations or Livewire components to validate database interactions and modal behaviour.

## Housekeeping

- Named routes live in `routes/backoffice.php` and are automatically prefixed with `backoffice.` by `RouteServiceProvider`.
- Role-aware navigation links live in `resources/views/components/backoffice/nav-link.blade.php`.
- Toast helper listens for `toast` browser events globally to keep components decoupled.

Happy shipping! 🚢
