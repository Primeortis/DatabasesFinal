use jopking;

CREATE TABLE category(
	name 			VARCHAR(32) PRIMARY KEY,
    description		VARCHAR(256)
);
CREATE TABLE product(
	p_id 			INT PRIMARY KEY,
    name			VARCHAR(32) NOT NULL,
    description		VARCHAR(256),
    price			NUMERIC(8,2) NOT NULL,
    stock			INT NOT NULL,
    adv_thres		INT,
    image			VARCHAR(256),
    discontinued	BOOL,
    cat_name		VARCHAR(32),
    FOREIGN KEY (cat_name) REFERENCES category(name)
		ON UPDATE CASCADE
		ON DELETE SET NULL
);
CREATE TABLE customer(
	c_id			INT PRIMARY KEY,
    first_name		VARCHAR(32) NOT NULL,
    last_name		VARCHAR(32) NOT NULL,
    email			VARCHAR(32) NOT NULL,
    username		VARCHAR(32) NOT NULL,
    password		VARCHAR(64) AS (SHA2(something,256)) NOT NULL,
    address			VARCHAR(128) NOT NULL
);
CREATE TABLE cart(
	c_id 			INT PRIMARY KEY,
    subtotal		NUMERIC(10,2) DEFAULT 0.00,
    FOREIGN KEY (c_id) REFERENCES customer(c_id) 
);
CREATE TABLE employee(
	e_id 			INT PRIMARY KEY,
    email			VARCHAR(32) NOT NULL,
    username		VARCHAR(32) NOT NULL,
    password		VARCHAR(64) AS (SHA2(something,256)) NOT NULL
);
CREATE TABLE cart_item(
	p_id			INT NOT NULL,
    c_id			INT NOT NULL,
    PRIMARY KEY (product, cart_of),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (c_id) REFERENCES cart(c_id)
		ON UPDATE CASCADE
);
CREATE TABLE orders(
	o_id			INT PRIMARY KEY,
    c_id 			INT NOT NULL,
    FOREIGN KEY (c_id) REFERENCES customer(c_id)
);
CREATE TABLE order_item(
	p_id 			INT NOT NULL,
    o_id			INT NOT NULL,
    PRIMARY KEY (p_id, o_id),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (o_id) REFERENCES orders(o_id)
		ON UPDATE CASCADE
);	
CREATE TABLE price_history(
    p_id			INT NOT NULL,
    time			TIMESTAMP NOT NULL,
    price_before	NUMERIC(8,2),
    price_after		NUMERIC(8,2),
    operation		VARCHAR(16),
    PRIMARY KEY (product, time),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE
);
CREATE TABLE stock_history(
    p_id			INT NOT NULL,
    time			TIMESTAMP NOT NULL,
	o_id 			INT,
    val1			NUMERIC(8,2),
    val2			NUMERIC(8,2),
    operation		VARCHAR(16),
    PRIMARY KEY (product, time),
    FOREIGN KEY (p_id) REFERENCES product(p_id)
		ON UPDATE CASCADE,
    FOREIGN KEY (o_id) REFERENCES orders(o_id)
		ON UPDATE CASCADE
);