use jopking;

DELIMITER //

DROP TRIGGER IF EXISTS product_id//
DROP TRIGGER IF EXISTS product_delete_prevention//

DROP PROCEDURE IF EXISTS create_employee//
CREATE PROCEDURE create_employee(
	IN id 				INT,
    IN email 			VARCHAR(32),
    IN username 		VARCHAR(32),
    IN temp_pass 		VARCHAR(32)
)
BEGIN
	IF (id IS NOT NULL AND email IS NOT NULL AND username IS NOT NULL AND temp_pass IS NOT NULL) THEN
		INSERT INTO employee
			VALUES (id, email, username, temp_pass);
	END IF;
END//

DROP PROCEDURE IF EXISTS insert_category//
CREATE PROCEDURE insert_category(
	IN name 			VARCHAR(32),
    IN description		VARCHAR(256)
)
BEGIN
	IF (name IS NOT NULL) THEN
		INSERT INTO category
			VALUES (name, description);
	END IF;
END//

DROP PROCEDURE IF EXISTS log_product_update//
CREATE PROCEDURE log_product_update(
	IN in_product_id 	INT,
    IN in_action_type 	ENUM('INSERT', 'UPDATE', 'DELETE'),
    IN in_old_price 	DECIMAL(10,2),
    IN in_new_price 	DECIMAL(10,2),
    IN in_old_stock 	INT,
    IN in_new_stock 	INT,
    IN in_employee_id 	INT,
    IN in_customer_id 	INT,
    IN in_order_id 		INT
)
BEGIN

IF (in_product_id IS NOT NULL) THEN
	CASE
	-- Handle Updates to Price/Stock
	WHEN (in_action_type = 'UPDATE') THEN
		-- Handle Price Changes
		IF (in_old_price IS NOT NULL AND in_new_price IS NOT NULL AND in_employee_id IS NOT NULL) THEN
			INSERT INTO price_history VALUES (in_product_id, current_timestamp(), in_old_price, in_new_price, 'UPDATE', in_employee_id);
		END IF;
		-- Handle Stock Changes
		IF (in_old_stock IS NOT NULL OR in_new_stock IS NOT NULL) THEN
			IF (in_employee_id IS NOT NULL) THEN
				INSERT INTO stock_history (p_id, time, stock_before, stock_after, operation, e_id) 
					VALUES (in_product_id, current_timestamp(), in_old_stock, in_new_stock, 'UPDATE', in_employee_id);
			END IF;
			IF (in_customer_id IS NOT NULL AND in_order_id IS NOT NULL) THEN
				INSERT INTO stock_history (p_id, time, stock_before, stock_after, operation, c_id, o_id) 
					VALUES (in_product_id, current_timestamp(), in_old_stock, in_new_stock, 'UPDATE', in_customer_id, in_order_id);
			END IF;
		END IF;
	-- Logging Insertion Operations
	WHEN (in_action_type = 'INSERT') THEN
		IF (in_new_price IS NOT NULL AND in_employee_id IS NOT NULL OR in_new_stock IS NOT NULL) THEN
			INSERT INTO stock_history (p_id, time, stock_after, operation, e_id)
				VALUES (in_product_id, current_timestamp(), in_new_stock, 'INSERT', in_employee_id);
			INSERT INTO price_history (p_id, time, price_after, operation, e_id)
				VALUES (in_product_id, current_timestamp(), in_new_price, 'INSERT', in_employee_id);
		END IF;
	-- Logging Deletions
	WHEN (in_action_type = 'DELETE') THEN
		IF (in_new_price IS NOT NULL AND in_employee_id IS NOT NULL OR in_new_stock IS NOT NULL) THEN
			INSERT INTO stock_history (p_id, time, stock_after, operation, e_id)
				VALUES (in_product_id, current_timestamp(), in_new_stock, 'INSERT', in_employee_id);
		END IF;
	END CASE;
END IF;
END//

DROP PROCEDURE IF EXISTS insert_product//
CREATE PROCEDURE insert_product(
	IN id				INT,
    IN name				VARCHAR(32),
    IN description		VARCHAR(256),
    IN price			DECIMAL(10,2),
    IN stock			INT,
    IN thres			INT,
    IN image 			VARCHAR(256),
    IN cat_name			VARCHAR(32),
    IN e_id				INT
)
BEGIN
	IF(id IS NOT NULL AND name IS NOT NULL AND price IS NOT NULL AND stock IS NOT NULL) THEN
		INSERT INTO product (p_id, name, description, price, stock, adv_thres, image, cat_name)
			VALUES (id, name, description, price, stock, thres, image, cat_name);
		CALL log_product_update (
			id, 'INSERT',
            NULL, price,
            NULL, stock,
            e_id, NULL, NULL
		);
	END IF;
END//

DROP TRIGGER IF EXISTS product_id//
CREATE TRIGGER product_id BEFORE UPDATE ON product 
FOR EACH ROW 
BEGIN
	SIGNAL SQLSTATE VALUE '45000'
		SET MESSAGE_TEXT = ' The prod id is not allowed to be changed';
END//

DROP TRIGGER IF EXISTS product_delete_prevention//
CREATE TRIGGER product_delete_prevention BEFORE DELETE ON product
FOR EACH ROW
BEGIN
	SIGNAL SQLSTATE VALUE '46000'
		SET MESSAGE_TEXT = ' The prod is not allowed to be deleted';
END//

DROP PROCEDURE IF EXISTS checkout//
CREATE PROCEDURE checkout(
	IN customer_id 				INT,
    OUT order_id				INT,
    OUT out_of_stock_product	INT
)
BEGIN
DECLARE i INT UNSIGNED DEFAULT 0;
SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;
START TRANSACTION;

INSERT INTO orders (c_id) VALUES (customer_id);

CREATE TABLE temp_cart SELECT p_id, quantity, stock, price FROM cart_item NATURAL JOIN product WHERE cart_item.c_id = customer_id; 
CREATE TABLE item SELECT * FROM temp_cart LIMIT 1;

GetItems: LOOP
	-- How to select the i-th row of the cart
    -- SELECT * FROM temp_cart LIMIT i, 1;
    -- Check if stock is sufficient
    SET @new_stock = (SELECT stock FROM item) - (SELECT quantity FROM item);
    IF (new_stock >= 0) THEN
		-- Convert item in temp_cart to order_item
        INSERT INTO order_item VALUES ((SELECT p_id FROM item), last_insert_id(), (SELECT quantity FROM item));
		-- Remove Stock of Item
        UPDATE product
        SET stock = new_stock
        WHERE p_id = (SELECT p_id FROM item);
        -- Update Total
        UPDATE orders SET total = total + (SELECT price FROM item) WHERE o_id = last_insert_id();
		-- Remove item from cart_items
        DELETE FROM cart_items WHERE p_id = (SELECT p_id FROM item) and c_id = customer_id;
    ELSE
		-- Abort Transaction
        rollback;
        SET out_of_stock_product = (SELECT p_id FROM item);
	END IF;
    -- Increment the iterator and get the next item
    SET i = i + 1;
    DELETE FROM item;
    INSERT INTO item SELECT * FROM temp_cart limit i, 1;
    IF (i = (SELECT count(*) FROM temp_cart)) THEN
		LEAVE GetItems;
	END IF;
END LOOP GetItems;

DROP TABLE temp_cart;
COMMIT;
END//

DELIMITER ;