# Testing Documentation

The Postman collection covers listing products, reading a product, adding a product, calling both SQL functions, stock-in, stock-out, reading transactions, and listing categories/suppliers. Negative requests cover 100-unit stock-out, zero stock-in, an unknown product, a negative product price, and negative initial stock.

Collection variables include `baseUrl`, `categoryId`, `supplierId`, `productId`, and `missingProductId`. Set category and supplier IDs from the seeded database. Successful product creation updates the `productId` variable.

## Verification state

Confirmed during generation: Laravel dependencies installed; Laravel's generated SQLite migrations completed; all ten API routes appear in `artisan route:list`; the routine source contains three procedures, two stock-row locks, two transaction inserts, and explicit exception handling.

Not confirmed: live SQL creation, Supabase connectivity, endpoint execution against PostgreSQL, successful/failed movement behavior, and transaction row counts. No Supabase credentials were supplied and no local PostgreSQL CLI was available. Run the Postman collection and `05_verification_queries.sql` after configuring a Supabase project; only then should those integration cases be marked passed.

For negative stock movement verification, record the product's stock and transaction count before the 100-unit request. Expect HTTP 422 and no change to either value afterward. Repeat the before/after comparison for zero quantity and unknown product requests.