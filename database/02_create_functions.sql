CREATE OR REPLACE FUNCTION fn_get_stock_value(p_product_id BIGINT)
RETURNS NUMERIC(22, 2)
LANGUAGE SQL
STABLE
AS $$
    SELECT unit_price * stock_quantity
    FROM products
    WHERE id = p_product_id;
$$;

CREATE OR REPLACE FUNCTION fn_get_stock_status(p_product_id BIGINT)
RETURNS TEXT
LANGUAGE SQL
STABLE
AS $$
    SELECT CASE
        WHEN stock_quantity = 0 THEN 'OUT OF STOCK'
        WHEN stock_quantity <= minimum_stock THEN 'LOW STOCK'
        ELSE 'AVAILABLE'
    END
    FROM products
    WHERE id = p_product_id;
$$;