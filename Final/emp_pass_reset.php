<?php
    session_start();
    require "db.php";
    if(!isset($_SESSION['username'])){
        header('Location: https://classdb.it.mtu.edu/~jopking/Final/emp_login.php');
    }
    if(isset($_POST['newPass'])){
        if(str_contains($_POST["newPass"], "test")){
            echo "<body>Your new password appears to still be a temporary password, please change it.</body>";
        }
        else{
            changeEmpPass($_SESSION["username"], $_POST["newPass"]);
            $_SESSION["newPass"] = 1;
            header('Location: https://classdb.it.mtu.edu/~jopking/Final/emp_login.php');
        }
    }
?>
<html>
    <h1>
        Employee Password Reset
    </h1>
    <h2>
        Your password meets the requirements to be a temporary password, please change your password
    </h2>
    <form method = "POST">
        <label>New Password</label>
        <input type="text" name = "newPass">
        <input type="submit" value = "Change Password">
    </form>
</html>