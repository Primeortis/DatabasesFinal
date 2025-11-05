-- Add the Employees
CALL create_employee (1, "employOne", "empone@gmail.com", "testPass1");
CALL create_employee (2, "employTwo", "emptwo@gmail.com", "testPass2");

-- Add the Customers
INSERT INTO customer (first_name, last_name, email, username, password, address)
	VALUES 	("Ann", "Marie", "am@mtu.edu", "annMarie", "password", "1701 Townsend Dr, Houghton, MI 49931"),
			("Bob", "Seger", "bs@mtu.edu", "bobSeger", "ThisIsSecure", "1702 Hillside Dr, Hancock, MI 49931"),
            ("Tom", "Hanks", "th@gmail.com", "tomHanks", "TommyIzCool", "1513 Jefferson Rd, Hancock, MI 49931");

-- Make the Categories
CALL insert_category ("Electronics", "Technology to make your house modern!");
CALL insert_category ("Food", "The basic necessity of life!");
CALL insert_category ("Home Cleaning", "Germaphobe’s friend!");
CALL insert_category ("Pet Care", "Take care of your fluffy friends!");

-- Insert Products
CALL insert_product (1, "Banana", "Yellow Fruit", 0.99, 35, 50, "/images/banana.png", "Food");
CALL insert_product (2, "Lysol", "Cleaning Spray", 7.50, 12, 10, "/images/lysol.png", "Home Cleaning");
CALL insert_product (3, "Wipes", "Sanitizing Cloth", 3.99, 17, 20, "/images/wipes.png", "Home Cleaning");
CALL insert_product (4, "Nintendo Switch", "Nintendo Brand Console", 499.99, 0, 5, "/images/nintendo_switch.png", "Electronics");
CALL insert_product (5, "Dog Food", "Average corn meal pellets", 21.99, 11, 10, "/images/dog_food.png", "Pet Care");
CALL insert_product (6, "Cat Food", "Made with hand caught salmon!", 24.99, 5, 7, "/images/cat_food.png", "Pet Care");
CALL insert_product (7, "Fitbit", "Step Counting Watch", 74.99, 1, 3, "/images/fitbit.png", "Electronics");
CALL insert_product (8, "Lettuce", "Eww green food", 7.99, 2, 5, "/images/lettuce.png", "Food");
CALL insert_product (9, "Bread", "Slap some butter on it", 6.50, 45, 60, "/images/bread.png", "Food");
CALL insert_product (10, "Broom", "Sweep up that mess", 12.25, 6, 5, "/images/broom.png", "Home Cleaning");

-- Add Items to carts
INSERT INTO cart_item VALUES (1, 2, 2);
INSERT INTO cart_item VALUES (7, 2, 1);
INSERT INTO cart_item VALUES (9, 2, 1);

INSERT INTO cart_item VALUES (8, 1, 2);
INSERT INTO cart_item VALUES (10, 1, 1);
INSERT INTO cart_item VALUES (6, 1, 5);

-- Add Orders
INSERT INTO orders (c_id, date, status, total) VALUES
(1, "2025-10-06 09:32:00", 'SHIPPED', 72.49),
(2, "2025-10-07 14:16:00", 'SHIPPED', 59.90),
(1, "2025-10-07 20:45:00", 'SHIPPED', 55.96),
(3, "2025-10-08 16:58:00", 'SHIPPED', 26.99),
(2, "2025-10-10 06:59:00", 'SHIPPED', 130.00),
(1, "2025-10-11 12:01:00", 'SHIPPED', 22.48),
(3, "2025-10-11 14:45:00", 'SHIPPED', 7.98),
(2, "2025-10-12 15:18:00", 'SHIPPED', 260.00),
(1, "2025-10-13 12:05:00", 'SHIPPED', 40.97),
(2, "2025-10-13 16:00:00", 'SHIPPED', 2499.95);

