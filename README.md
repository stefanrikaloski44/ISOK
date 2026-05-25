# Grand Horizon Hotel Management System — Project Documentation

## Overview
Grand Horizon is a Laravel-based hotel management and reservation platform with:

- Public hotel booking website
- Guest reservation system
- Admin dashboard
- Room management
- Reservation management
- Authentication and role-based access
- PostgreSQL database
- TailwindCSS frontend styling

---

# Tech Stack

## Backend
- PHP 8.2
- Laravel 12
- PostgreSQL
- Eloquent ORM
- Laravel Breeze (authentication)

## Frontend
- Blade templates
- TailwindCSS
- Vite

## Tooling
- Composer
- NPM
- Git + GitHub
- XAMPP (PHP runtime)

---

# Authentication

Authentication is handled using Laravel Breeze.

Features:
- Login
- Register
- Logout
- Session handling
- Admin middleware

## Admin Middleware

```php
if (!auth()->check() || !auth()->user()->is_admin) {
    abort(403, 'Access denied.');
}
```

---

# Database Structure

## Users

```text
users
- id
- name
- email
- password
- is_admin
- created_at
- updated_at
```

## Rooms

```text
rooms
- id
- room_number
- type
- description
- price_per_night
- capacity
- floor
- status
- amenities
- created_at
- updated_at
```

## Guests

```text
guests
- id
- first_name
- last_name
- email
- phone
- created_at
- updated_at
```

## Reservations

```text
reservations
- id
- reservation_number
- room_id
- guest_id
- check_in_date
- check_out_date
- guests_count
- total_amount
- paid_amount
- payment_status
- status
- special_requests
- created_at
- updated_at
```

## Payments

```text
payments
- id
- reservation_id
- amount
- type
- paid_at
- created_at
- updated_at
```

---

# Main Features

## Public Features

### Homepage
- TailwindCSS design
- Hero section
- Browse rooms button

### Room Browsing
Users can:
- View rooms
- Filter by:
  - Type
  - Price
  - Availability

### Reservation Booking
Users can:
- Select room
- Choose dates
- Enter guest info
- Create reservation

### My Reservations
Authenticated users can:
- View their reservations
- Check reservation history

---

# Admin Features

## Dashboard
Admin dashboard includes:

- Total rooms
- Available rooms
- Occupied rooms
- Total guests
- Active reservations
- Revenue statistics
- Upcoming check-ins

## Room Management
Admin can:
- Create rooms
- Edit rooms
- Delete rooms
- View room history

## Reservation Management
Admin can:
- View reservations
- Check-in guests
- Check-out guests
- Cancel reservations
- Add payments

---

# Setup Instructions

## 1. Clone Repository

```bash
git clone https://github.com/stefanrikaloski44/ISOK.git
```

## 2. Install PHP Dependencies

```bash
composer install
```

## 3. Install Frontend Dependencies

```bash
npm install
```

## 4. Create Environment File

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
copy .env.example .env
```

## 5. Generate Application Key

```bash
php artisan key:generate
```

## 6. Run Migrations

```bash
php artisan migrate
```

## 7. Create Storage Link

```bash
php artisan storage:link
```

## 8. Start Vite

```bash
npm run dev
```

## 9. Start Laravel Server

```bash
php artisan serve
```

---

# Creating an Admin User

Using Tinker:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'is_admin' => true,
]);
```

---

# Important Laravel Commands

## Clear Route Cache

```bash
php artisan route:clear
```

## Clear Application Cache

```bash
php artisan cache:clear
```

## Clear Config Cache

```bash
php artisan config:clear
```

## View Routes

```bash
php artisan route:list
```

## Run Migrations

```bash
php artisan migrate
```

## Rollback Migrations

```bash
php artisan migrate:rollback
```

---

# UI Design Notes

## Design Style
- Luxury hotel aesthetic
- Dark hero section
- Gold accent palette
- Minimalistic cards
- Tailwind utility-first styling

## Homepage
- Fullscreen hero section
- Non-scrollable layout
- Sticky navigation
- Footer integrated into layout

