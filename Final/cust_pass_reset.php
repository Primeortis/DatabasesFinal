<?php
    session_start();
    require "common.php";
    if(!isset($_SESSION['username'])){
        session_destroy();
        header('Location: https://classdb.it.mtu.edu/~jopking/Final/cust_login.php');
    }
    if(isset($_POST['newPass'])){
        changeCustPass($_SESSION["username"], $_POST["newPass"]);
        $_SESSION["newPass"] = 1;
        header('Location: https://classdb.it.mtu.edu/~jopking/Final/cust_login.php');
    }
?>
<html>
    <h1>
        Customer Password Reset
    </h1>
    <h2>
        Please input your new password
    </h2>
    <form method = "POST">
        <label>New Password</label>
        <input type="text" name = "newPass">
        <input type="submit" value = "Change Password">
    </form>
</html>