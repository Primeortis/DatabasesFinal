<?php
    session_start();
    require "common.php";
    if(isset($_POST["logout"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
?>
<html>
    <h1>Welcome to The Store of Your Dreams</h1>
    <h2>At least we hope it is . . .</h2>

    <form method = "POST">
        <label></label>
    </form>

    <?php
    
        $products = getCart();
        printProducts($products);
    
        $products2 = getOrder(oid: 1);
        printProducts($products2);
    ?>

    <form method = "POST" action="main.php">
        <input type="submit" name="logout" value="logout">
    </form>
</html>