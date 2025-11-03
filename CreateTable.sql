use jopking;

DROP TABLE IF EXISTS category, product, customer, cart, employee, cart_item, orders, order_item, price_history, stock_history;

DROP TABLE IF EXISTS category;
CREATE TABLE category(
	name 			VARCHAR(32) PRIMARY KEY,
    description		VARCHAR(256)
);

DROP TABLE IF EXISTS product;
CREATE TABLE product(
	p_id 			INT PRIMARY KEY,
    name			VARCHAR(32) NOT NULL,
    description		VARCHAR(256),
    price			NUMERIC(10,2) NOT NULL,
    stock			INT NOT NULL,
    adv_thres		INT,
    image			VARCHAR(256),
    discontinued	BOOL,
    cat_name		VARCHAR(32),
    FOREIGN KEY (cat_name) REFERENCES category(name)
		ON UPDATE CASCADE
		ON DELETE SET NULL
);

DROP TABLE IF EXISTS customer;
CREATE TABLE customer(
	c_id			INT PRIMARY KEY,
    first_name		VARCHAR(32) NOT NULL,
    last_name		VARCHAR(32) NOT NULL,
    email			VARCHAR(32) NOT NULL,
    username		VARCHAR(32) NOT NULL,
    password		BINARY(32),
    address			VARCHAR(128) NOT NULL
);

DROP TABLE IF EXISTS cart;
CREATE TABLE cart(
	c_id 			INT PRIMARY KEY,
    subtotal		NUMERIC(10,2) DEFAULT 0.00,
    FOREIGN KEY (c_id) REFERENCES customer(c_id) 
);

DROP TABLE IF EXISTS employee;
CREATE TABLE employee(
	e_id 			INT PRIMARY KEY,
    email			VARCHAR(32) NOT NULL,
    username		VARCHAR(32) NOT NULL,
    password		BINARY(32)
);

DROP TABLE IF EXISTS cart_item;
CREATE TABLE cart_item(
	p_id			INT NOT NULL,
    c_id			INT NOT NULL,
    quantity		INT NOT NULL DEFAULT 0,
    PRIMARY KEY (p_id, c_id),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (c_id) REFERENCES cart(c_id)
		ON UPDATE CASCADE
);

DROP TABLE IF EXISTS orders;
CREATE TABLE orders(
	o_id			INT PRIMARY KEY,
    c_id 			INT NOT NULL,
    date			TIMESTAMP NOT NULL,
    status 			ENUM("PLACED", "SHIPPED", "CLOSED"),
    total 			NUMERIC(14,2),
    FOREIGN KEY (c_id) REFERENCES customer(c_id)
);

DROP TABLE IF EXISTS order_item;
CREATE TABLE order_item(
	p_id 			INT NOT NULL,
    o_id			INT NOT NULL,
    quantity		INT NOT NULL DEFAULT 0,
    PRIMARY KEY (p_id, o_id),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (o_id) REFERENCES orders(o_id)
		ON UPDATE CASCADE
);	

DROP TABLE IF EXISTS price_history;
CREATE TABLE price_history(
    p_id			INT NOT NULL,
    time			TIMESTAMP NOT NULL,
    price_before	NUMERIC(10,2) DEFAULT 0.00,
    price_after		NUMERIC(10,2) DEFAULT 0.00,
    operation		ENUM('INSERT', 'UPDATE', 'DELETE'),
    e_id			INT,
    PRIMARY KEY (p_id, time),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (e_id) REFERENCES employee(e_id)
		ON UPDATE CASCADE
);

DROP TABLE IF EXISTS stock_history;
CREATE TABLE stock_history(
    p_id			INT NOT NULL,
    time			TIMESTAMP NOT NULL,
    stock_before	NUMERIC(8) DEFAULT 0,
    stock_after		NUMERIC(8) DEFAULT 0,
    operation		ENUM('INSERT', 'UPDATE', 'DELETE'),
	o_id 			INT DEFAULT NULL,
    c_id			INT DEFAULT NULL,
    e_id			INT DEFAULT NULL,
    PRIMARY KEY (p_id, time),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (o_id) REFERENCES orders(o_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (e_id) REFERENCES employee(e_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (c_id) REFERENCES customer(c_id)
		ON UPDATE CASCADE
);