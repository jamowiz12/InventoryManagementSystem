# API Documentation

Base URL during local development: `http://127.0.0.1:8000/api`. Requests and responses use JSON. Successful responses have `{ "success": true, "message": "...", "data": ... }`; errors have the same keys with `success: false` and `data: null`. Laravel validation errors return HTTP 422.

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/products` | List products with category and supplier. |
| GET | `/products/{id}` | Return one product. |
| POST | `/products` | Create through `sp_add_product`; requires category, supplier, product name, price, stock, and minimum stock. |
| POST | `/stock-in` | Execute `sp_stock_in`; requires `product_id`, positive `quantity`, optional `remarks`. |
| POST | `/stock-out` | Execute `sp_stock_out`; same body; insufficient stock returns 422. |
| GET | `/products/{id}/stock-value` | Call `fn_get_stock_value`. |
| GET | `/products/{id}/stock-status` | Call `fn_get_stock_status`. |
| GET | `/categories` | List categories. |
| GET | `/suppliers` | List suppliers. |
| GET | `/transactions` | List movements with associated products. |

The product and movement POST methods validate input before calling the database. SQL calls use parameter binding. A missing product on GET returns 404; a nonexistent product in a movement request is rejected by validation with 422.

## Supabase connection

Laravel uses PDO PostgreSQL. Set `DB_CONNECTION=pgsql`, `DB_PORT=5432`, `DB_DATABASE=postgres`, `DB_SSLMODE=require`, and the host, pooler username, and password in the untracked `backend/.env`. A Supabase session-pooler connection is appropriate for application clients. Do not put credentials in source files or Postman.