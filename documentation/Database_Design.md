# Database Design

The schema uses four related tables and PostgreSQL identity primary keys. Foreign keys use `ON DELETE RESTRICT` so referenced inventory records cannot be removed accidentally.

| Table | Purpose and main fields |
|---|---|
| `categories` | Product group; `category_name` is unique and `description` is optional. |
| `suppliers` | Supplier identity and optional contact information. |
| `products` | Product name, description, non-negative `unit_price`, `stock_quantity`, `minimum_stock`, and required category/supplier references. |
| `stock_transactions` | Append-only movement details: type, positive quantity, previous stock, resulting stock, remarks, and timestamp. |

## Relationships

- One category can have many products; each product has one category.
- One supplier can supply many products; each product has one supplier.
- One product can have many stock transactions; each transaction belongs to one product.

Indexes support product category/supplier lookups, product-name lookups, and recent transaction history by product. Check constraints reject negative prices or stock, unknown transaction types, non-positive movement quantities, and inconsistent transaction deltas.

The sample records include beverages, snacks, North Star Wholesale, and a sample water product. `database/00_initialize_inventory.sql` is the single-file database initializer. The numbered SQL files are the same setup divided by purpose.