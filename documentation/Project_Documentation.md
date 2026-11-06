# Simple Inventory Management System

## Introduction and business scenario

This project is a backend inventory service for a small retail store. Staff need to organize products, connect them to categories and suppliers, view inventory value and stock status, and keep an auditable record of stock movements.

## Objectives

- Store related inventory information using PostgreSQL primary and foreign keys.
- Enforce valid prices, quantities, and references in the database.
- Use PostgreSQL functions for stock value and stock status.
- Use PostgreSQL procedures for product creation and stock movements.
- Provide a Laravel JSON REST API that calls those database routines.

## System design

Laravel 12 handles HTTP routing, request validation, JSON responses, and PostgreSQL connectivity. Supabase PostgreSQL owns the inventory data and business-critical stock calculations. The stock procedures lock a product row and update its quantity and transaction log atomically.

## Setup overview

1. Run `database/00_initialize_inventory.sql` once in the Supabase SQL Editor, or run the split scripts `01` through `04` in order.
2. Copy `backend/.env.example` to `backend/.env` and enter the Supabase session-pooler host, username, and password. Never commit `.env`.
3. From `backend`, run `php artisan migrate` to add Laravel's own migration bookkeeping and inventory migration, then `php artisan serve`.
4. Import `postman/Inventory_Management_System.postman_collection.json` and run the requests against `http://127.0.0.1:8000/api`.

See `Database_Design.md`, `SQL_Functions_and_Procedures.md`, `API_Documentation.md`, and `Testing_Documentation.md` for details. Live integration tests require Supabase credentials and were not run during project generation.