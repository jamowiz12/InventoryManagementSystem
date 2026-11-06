-- Run after 01 through 04. These statements are read-only.
SELECT * FROM categories ORDER BY id;
SELECT * FROM suppliers ORDER BY id;
SELECT p.*, fn_get_stock_value(p.id) AS stock_value, fn_get_stock_status(p.id) AS stock_status
FROM products p ORDER BY p.id;
SELECT * FROM stock_transactions ORDER BY created_at DESC, id DESC;

-- Check that each recorded movement agrees with its resulting product stock.
SELECT t.id, t.product_id, t.transaction_type, t.quantity, t.previous_stock, t.new_stock
FROM stock_transactions t
WHERE (t.transaction_type = 'STOCK_IN' AND t.new_stock <> t.previous_stock + t.quantity)
   OR (t.transaction_type = 'STOCK_OUT' AND t.new_stock <> t.previous_stock - t.quantity);