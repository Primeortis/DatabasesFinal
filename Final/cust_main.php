<?php
    session_start();
    require "common.php";
    if(isset($_POST["logout"]) or !isset($_SESSION['username'])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
    if(isset($_POST["checkout"])){
        $_POST["orderDetails"] = checkout();
    }
    if(isset($_POST["seeProducts"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/products.php' );
    }
    if(isset($_POST["update"])){
        updateCartItem($_POST['p_id'], $_POST['quantity']);
    }
    if(isset($_POST["remove"])){
        deleteCartItem($_POST['p_id']);
    }
?>
<html>
    <h1>Welcome to The Store of Your Dreams</h1>
    <h2>At least we hope it is . . .</h2>

    <form method = "POST" action="products.php">
        <input type="submit" name="show products" value="View All Products">
    </form>
    <form method = "POST">
        <input type = 'submit' name = "seeCart" value = "Show My Cart"> </br></br>
        <label>Order Id</label>
        <input type = 'number' name = "oid">
        <input type = 'submit' name = "seeOrder" value = "See Order">
    </form>

    <?php
        if(isset($_POST['seeCart'])){
            $products = getCart();
            if($products != []){
                PrintCart( $products );
                echo"
                    <form method = 'POST'>
                        <input type = 'submit' name = 'checkout' value = 'Checkout'>
                    </form>
                ";
            } else {
                echo "<h4 style='color:red'> Your cart is empty!</h4>";
            }
        }
        if(isset($_POST["seeOrder"])){
            if(authenticateOrder($_POST["oid"])[0] >= 1){
                $products = getOrder(($_POST["oid"]));
                printOrder($products);
            } else {
                echo "<h4 style='color:red'>Not Your Order!</h4>";
            }
        }
        if(isset($_POST["checkout"])){
            echo "
            <h3 style = 'color:green'>Order #", $_POST["orderDetails"]['o_id'], " Placed! </h3>
            <h3 style = 'color:green'>Total: $", $_POST["orderDetails"]["total"], "</h3>";
        }
    ?>
    <form method = "POST" action="cust_pass_reset.php">
        <input type="submit" name="change password" value="Change Password">
    </form>
    <form method = "POST" action="main.php">
        <input type="submit" name="logout" value="Logout">
    </form>
</html>