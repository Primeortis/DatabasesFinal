<?php
    session_start();
    require "common.php";
    if(isset($_POST["logout"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
    if(isset($_POST['addCard'])){
        
    }
?>
<html>
    <h1>Welcome to The Store of Your Dreams</h1>
    <h2>At least we hope it is . . .</h2>
    <form method = 'POST' action = 'main.php'>
        <input type = 'submit' value = 'Return to Main Menu'>
    </form>
    <?php
        if(isset($_SESSION["username"])){
            echo"
                <form method = 'POST' action = 'cust_main.php'>
                    <input type = 'submit' value = 'Return to Cart'>
                </form>
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