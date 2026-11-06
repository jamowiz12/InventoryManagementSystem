# Simple Inventory Management System

Backend-only Laravel 12 and Supabase PostgreSQL DBMS project.

## Project layout

- `backend/` Laravel API, models, routes, migration, and `.env.example`.
- `database/` single-file initializer, split PostgreSQL scripts, and read-only verification queries.
- `postman/` API collection with positive and negative requests.
- `documentation/` project, database, SQL routine, API, and test documentation.

## Run

1. Run `database/00_initialize_inventory.sql` once using Supabase SQL Editor, or use the Laravel inventory migration instead.
2. Copy `backend/.env.example` to `backend/.env`; provide Supabase host, pooler username, and database password. Keep `.env` private.
3. Open a terminal in `backend`, run `composer install` if needed, `php artisan migrate`, then `php artisan serve`.
4. Import `postman/Inventory_Management_System.postman_collection.json`. Set IDs to match the initialized sample data and send requests to `http://127.0.0.1:8000/api`.

Required local tools include PHP 8.2 or newer with `pdo_pgsql` and Composer. See `documentation/Testing_Documentation.md` for checks that have and have not been executed. Live database verification is pending Supabase credentials.