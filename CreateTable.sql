use jopking;

CREATE TABLE category(
	name 			VARCHAR(32) PRIMARY KEY,
    description		VARCHAR(256)
);
CREATE TABLE product(
	p_id 			INT PRIMARY KEY,
    name			VARCHAR(32),
    description		VARCHAR(256),
    price			NUMERIC(8,2),
    stock			INT,
    adv_thres		INT,
    image			VARCHAR(256),
    discontinued	BOOL,
    cat_name		VARCHAR(32),
    FOREIGN KEY (cat_name) references category(name)
);
CREATE TABLE customer(
	c_id			INT PRIMARY KEY,
    first_name		VARCHAR(32),
    last_name		VARCHAR(32),
    email			VARCHAR(32),
    username		VARCHAR(32),
    password		VARCHAR(64) AS (SHA2(something,256)) NOT NULL,
    address			VARCHAR(128),
    subtotal		NUMERIC(10,2)
);
CREATE TABLE employee(
	e_id 			INT PRIMARY KEY,
    email			VARCHAR(32),
    username		VARCHAR(32),
    password		VARCHAR(64) AS (SHA2(something,256)) NOT NULL
);
CREATE TABLE cart_item(
	p_id			INT,
    c_id			INT,
    PRIMARY KEY (product, cart_of),
    FOREIGN KEY (p_id) REFERENCES product(p_id),
    FOREIGN KEY (c_id) REFERENCES customer(c_id)
);
CREATE TABLE orders(
	o_id			INT PRIMARY KEY,
    c_id 			INT,
    FOREIGN KEY (c_id) REFERENCES customer(c_id)
);
CREATE TABLE order_item(
	p_id 			INT,
    o_id			INT,
    PRIMARY KEY (p_id, o_id),
    FOREIGN KEY (p_id) REFERENCES product(p_id),
    FOREIGN KEY (o_id) REFERENCES orders(o_id)
);
CREATE TABLE product_history(
    p_id			INT,
    time			TIMESTAMP,
	o_id 			INT,
    val1			NUMERIC(8,2),
    val2			NUMERIC(8,2),
    operation		VARCHAR(16),
    PRIMARY KEY (product, time),
    FOREIGN KEY (p_id) REFERENCES product(p_id),
    FOREIGN KEY (o_id) REFERENCES orders(o_id)
);