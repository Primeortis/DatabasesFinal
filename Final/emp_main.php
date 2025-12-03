<?php
    session_start();
    require "common.php";
    if(!isset($_SESSION["username"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/cust_login.php' );
    }
    if(isset($_POST["logout"])){
        header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
?>
<html>
    <h1>
        Welcome to The Store of Your Nightmares
    </h1>
    <h2>
        We don't pay you enough, but you work anyway!
    </h2>
    <form method = "POST">
        <input type="submit" name="logout" value="logout">
    </form>
</html>