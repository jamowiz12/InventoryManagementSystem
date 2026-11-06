# SQL Functions and Procedures

## Functions

- `fn_get_stock_value(product_id)` returns `unit_price * stock_quantity` as `NUMERIC(22,2)`. A nonexistent ID returns SQL `NULL`; the API checks the product first and returns HTTP 404.
- `fn_get_stock_status(product_id)` returns `OUT OF STOCK` at zero, `LOW STOCK` at or below the minimum, and `AVAILABLE` otherwise. A nonexistent ID returns SQL `NULL`.

Laravel calls each function using a bound parameter in a `SELECT`; it does not duplicate the formulas in PHP.

## Procedures

- `sp_add_product` checks required values and category/supplier existence, inserts the product, and returns its identity using an `INOUT` parameter.
- `sp_stock_in` validates a positive quantity, locks the product row with `FOR UPDATE`, increments stock, and records the movement.
- `sp_stock_out` validates the quantity, locks the row, rejects insufficient stock, decrements stock, and records the movement.

Each procedure call is one PostgreSQL statement. An exception rolls back all changes made by that statement. Laravel also wraps stock procedure calls in a transaction. The stock transaction is inserted only after the quantity update succeeds.

Useful manual queries are in `database/05_verification_queries.sql`.