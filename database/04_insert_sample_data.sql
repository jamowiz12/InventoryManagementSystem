INSERT INTO categories (category_name, description) VALUES
    ('Beverages', 'Drinks and bottled beverages'),
    ('Snacks', 'Packaged snacks and convenience foods')
ON CONFLICT (category_name) DO NOTHING;

INSERT INTO suppliers (supplier_name, contact_person, contact_number, email, address)
SELECT 'North Star Wholesale', 'Jordan Lee', '+1-555-0100', 'orders@northstar.example', '12 Market Road'
WHERE NOT EXISTS (SELECT 1 FROM suppliers WHERE supplier_name = 'North Star Wholesale');

INSERT INTO products (category_id, supplier_id, product_name, description, unit_price, stock_quantity, minimum_stock)
SELECT c.id, s.id, 'Spring Water 500ml', 'Sample bottled water', 1.25, 20, 5
FROM categories c CROSS JOIN suppliers s
WHERE c.category_name = 'Beverages' AND s.supplier_name = 'North Star Wholesale'
  AND NOT EXISTS (SELECT 1 FROM products WHERE product_name = 'Spring Water 500ml');