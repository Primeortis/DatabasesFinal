<?php
    session_start();
    if(session_status()==PHP_SESSION_ACTIVE) session_destroy();
    session_start();
?>
<html>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <h1>The Store of Your Dreams</h1>
    <h2>We Tried . . .</h2>
    <div style = "padding:20; text-align:center;">
        <form method = "POST" action="products.php">
        <input type="submit" name="show products" value="View All Products">
        </form>
        <form method = "POST" action = "cust_login.php">
            <input type = "submit" value = "Customer Login">
        </form>
        <form method = "POST" action = "cust_register.php">
            <input type = "submit" value = "New Customers">
        </form>
        <form method = "POST" action = "emp_login.php">
            <input type = "submit" value = "Employees">
        </form>
    </div>
</html>