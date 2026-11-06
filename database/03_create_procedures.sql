CREATE OR REPLACE PROCEDURE sp_add_product(
    IN p_category_id BIGINT,
    IN p_supplier_id BIGINT,
    IN p_product_name VARCHAR,
    IN p_description TEXT,
    IN p_unit_price NUMERIC,
    IN p_stock_quantity INTEGER,
    IN p_minimum_stock INTEGER,
    INOUT p_product_id BIGINT
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF p_product_name IS NULL OR BTRIM(p_product_name) = '' THEN
        RAISE EXCEPTION 'Product name is required' USING ERRCODE = 'P0001';
    END IF;
    IF p_unit_price IS NULL OR p_unit_price < 0 THEN
        RAISE EXCEPTION 'Unit price cannot be negative' USING ERRCODE = 'P0001';
    END IF;
    IF p_stock_quantity IS NULL OR p_stock_quantity < 0 THEN
        RAISE EXCEPTION 'Stock quantity cannot be negative' USING ERRCODE = 'P0001';
    END IF;
    IF p_minimum_stock IS NULL OR p_minimum_stock < 0 THEN
        RAISE EXCEPTION 'Minimum stock cannot be negative' USING ERRCODE = 'P0001';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM categories WHERE id = p_category_id) THEN
        RAISE EXCEPTION 'Category does not exist' USING ERRCODE = 'P0001';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM suppliers WHERE id = p_supplier_id) THEN
        RAISE EXCEPTION 'Supplier does not exist' USING ERRCODE = 'P0001';
    END IF;

    INSERT INTO products (category_id, supplier_id, product_name, description, unit_price, stock_quantity, minimum_stock)
    VALUES (p_category_id, p_supplier_id, BTRIM(p_product_name), p_description, p_unit_price, p_stock_quantity, p_minimum_stock)
    RETURNING id INTO p_product_id;
END;
$$;

CREATE OR REPLACE PROCEDURE sp_stock_in(
    IN p_product_id BIGINT,
    IN p_quantity INTEGER,
    IN p_remarks TEXT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_previous_stock INTEGER;
    v_new_stock INTEGER;
BEGIN
    IF p_quantity IS NULL OR p_quantity <= 0 THEN
        RAISE EXCEPTION 'Stock-in quantity must be greater than zero' USING ERRCODE = 'P0001';
    END IF;
    SELECT stock_quantity INTO v_previous_stock FROM products WHERE id = p_product_id FOR UPDATE;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Product does not exist' USING ERRCODE = 'P0001';
    END IF;
    v_new_stock := v_previous_stock + p_quantity;
    UPDATE products SET stock_quantity = v_new_stock, updated_at = NOW() WHERE id = p_product_id;
    INSERT INTO stock_transactions (product_id, transaction_type, quantity, previous_stock, new_stock, remarks)
    VALUES (p_product_id, 'STOCK_IN', p_quantity, v_previous_stock, v_new_stock, p_remarks);
END;
$$;

CREATE OR REPLACE PROCEDURE sp_stock_out(
    IN p_product_id BIGINT,
    IN p_quantity INTEGER,
    IN p_remarks TEXT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_previous_stock INTEGER;
    v_new_stock INTEGER;
BEGIN
    IF p_quantity IS NULL OR p_quantity <= 0 THEN
        RAISE EXCEPTION 'Stock-out quantity must be greater than zero' USING ERRCODE = 'P0001';
    END IF;
    SELECT stock_quantity INTO v_previous_stock FROM products WHERE id = p_product_id FOR UPDATE;
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Product does not exist' USING ERRCODE = 'P0001';
    END IF;
    IF v_previous_stock < p_quantity THEN
        RAISE EXCEPTION 'Insufficient stock: available %, requested %', v_previous_stock, p_quantity USING ERRCODE = 'P0001';
    END IF;
    v_new_stock := v_previous_stock - p_quantity;
    UPDATE products SET stock_quantity = v_new_stock, updated_at = NOW() WHERE id = p_product_id;
    INSERT INTO stock_transactions (product_id, transaction_type, quantity, previous_stock, new_stock, remarks)
    VALUES (p_product_id, 'STOCK_OUT', p_quantity, v_previous_stock, v_new_stock, p_remarks);
END;
$$;