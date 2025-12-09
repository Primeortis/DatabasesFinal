<?php
    session_start();
    require "common.php";
?>
<html>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <h1>Customer Login</h1>
    <h2>Please input your credentials</h2>
<?php
    if (isset($_POST["logout"])) {
        session_destroy();
    }
    if (isset($_SESSION["new"])) {
        echo "<body>Please log in using your new username and password</body>";
    }
    if(isset($_POST["username"]) && isset($_POST["password"])){
        if (authenticateCust($_POST["username"], $_POST["password"]) ==1) {
            setCustomerSession($_POST['username']);
            header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/cust_main.php' );
        }else{
            echo '
            <p style="color:red">Incorrect Username or Password</p>
            <form action = "cust_register.php">
                <input type = "submit" value = "Register Here!">
            </form>';
        } 
    }
?>
<form method="POST">
    <text>username:</text>
    <input type = "text" name="username" required> <br>
    <text>password:</text>
    <input type = "password" name="password" required> <br>
    <input type = "submit" value = "login"> 
</form>
<form method = "POST" action="main.php">
    <input type="submit" name="return" value="Return to Main Menu">
</form>
</html>