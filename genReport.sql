USE jopking;

-- a) Historic prices
SET @target_product = 1;
SELECT
    ph.p_id AS product_id,
    p.name AS product_name,
    ph.time AS change_time,
    ph.price_before,
    ph.price_after,
    ph.operation,
    e.username AS updated_by
FROM price_history ph
JOIN product p ON ph.p_id = p.p_id
LEFT JOIN employee e ON ph.e_id = e.e_id
WHERE ph.p_id = @target_product
ORDER BY ph.time DESC;


-- b) Highest and lowest prices within a given period
SET @start_date = '2025-01-01';
SET @end_date = '2025-12-31';
SELECT
    p.p_id AS product_id,
    p.name AS product_name,
    MIN(ph.price_after) AS lowest_price,
    MAX(ph.price_after) AS highest_price
FROM product p
JOIN price_history ph ON p.p_id = ph.p_id
WHERE ph.time BETWEEN @start_date AND @end_date
GROUP BY p.p_id, p.name
ORDER BY p.name;


-- c) How many quantities sold for each product within a given period (ignore unsold)
SET @start_period = '2025-01-01';
SET @end_period = '2025-12-31';
SELECT
    oi.p_id AS product_id,
    p.name AS product_name,
    SUM(oi.quantity) AS total_sold
FROM order_item oi
JOIN orders o ON oi.o_id = o.o_id
JOIN product p ON oi.p_id = p.p_id
WHERE o.date BETWEEN @start_period AND @end_period
GROUP BY oi.p_id, p.name
HAVING total_sold > 0
ORDER BY total_sold DESC;


-- d) Products below their restocking threshold & quantity needed to reach the threshold
SELECT
    p.p_id AS product_id,
    p.name AS product_name,
    p.stock AS current_stock,
    p.adv_thres AS restock_threshold,
    (p.adv_thres - p.stock) AS quantity_needed
FROM product p
WHERE p.stock < p.adv_thres
ORDER BY quantity_needed DESC;


