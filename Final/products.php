<?php
    session_start();
    require "common.php";
    if(isset($_POST["logout"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
    if(isset($_POST['addCart'])){
        if ($qty === "" || !is_numeric($qty) || $qty <= 0){
            $_SESSION['error'] = "Please enter a valid quantity.";
            header("Location: https://classdb.it.mtu.edu/~jopking/Final/products.php");
            exit();
        }
        addToCart(pid: $_POST["p_id"], quantity: $_POST["quantity"]);
    }
?>
<html>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <h1>Welcome to The Store of Your Dreams</h1>
    <h2>At least we hope it is . . .</h2>
    <?php
        if(isset($_SESSION["username"])){
            echo"
                <form method = 'POST' action = 'cust_main.php'>
                    <input type = 'submit' value = 'Return to Main Menu'>
                </form>
            ";
        } else {
            echo"
                <form method = 'POST' action = 'main.php'>
                    <input type = 'submit' value = 'Return to Main Menu'>
                </form>
                <h3 style=\"text-align:left; padding-top:10\">Please login to add items to cart!</h3>
            ";
        }
    ?>
    <form method = "POST">
        <label>What category would you like to see?</label></br>
        <select name = "category">
            <option value = "All">Get All Items</option>;
            <?php
                categorySelect();
            ?>
        </select>
        <input type = "submit" value = "Get Products">
    </form>
    <?php
        if(isset($_POST['category'])){
            echo "<h3>Category: ", $_POST["category"],"</h3>";
            $products = getCategory($_POST['category']);
            printProducts($products);
        }
    ?>
</html>