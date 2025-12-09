<?php
    session_start();
    require "common.php";
    if(isset($_POST["return"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
?>
<html>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <?php
        if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])  && isset($_POST["first"]) && isset($_POST["last"])&& isset($_POST["address"])){
            addCustomer($_POST["username"], $_POST["password"], $_POST["email"], $_POST["first"], $_POST["last"], $_POST["address"]);
            $_SESSION["new"] = 1;
            header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/cust_login.php' );
        }
    ?>
    <h1>
        Customer Registration
    </h1>
    <h2>
        Please provide your information to create a new account at The Store!
    </h2>
    <form method = "POST">
        <label>First Name</label>
        <input type = "text" name = "first"> </br>
        <label>Last Name</label>
        <input type = "text" name = "last"> </br>
        <label>Email</label>
        <input type = "text" name = "email"> </br>
        <label>Address</label>
        <input type = "text" name = "address"> </br>
        <label>Username</label>
        <input type = "text" name = "username" required> </br>
        <label>Password</label>
        <input type = "text" name = "password" required> </br>
        <input type = "submit" value = "Register Account">
    </form>
    <form method = "POST" action="main.php">
        <input type="submit" name="return" value="Return to Main">
    </form>
</html>