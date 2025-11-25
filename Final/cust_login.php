<?php
    session_start();
    require "db.php";
?>
<html>
<?php
    if (isset($_POST["logout"])) {
        session_destroy();
    }
    if(isset($_POST["username"]) && isset($_POST["password"])){
        if (authenticateCust($_POST["username"], $_POST["password"]) ==1) {
            $_SESSION["username"] = $_POST["username"];
            header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/cust_main.php' );
        }else{
            echo '<p style="color:red">incorrect username and password</p>';
        } 
    }
?>
<form method="POST">
    <text>username:</text>
    <input type = "text" name="username"> <br>
    <text>password:</text>
    <input type = "text" name="password"> <br>
    <input type = "submit" value = "login"> 
</form>
</html>