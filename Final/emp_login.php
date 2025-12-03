<?php
    session_start();
    require "db.php";
    if (isset($_POST["logout"])) {
        session_destroy();
    }
    if (isset($_SESSION["newPass"])) {
        echo "Please log in with your new password";
    }
    if(isset($_POST["username"]) && isset($_POST["password"])){
        if (authenticateEmp($_POST["username"], $_POST["password"]) == 1) {
            setEmployeeSession($_POST["username"]);
            if(str_contains($_POST["password"], "test")){
                header('Location: https://classdb.it.mtu.edu/~jopking/Final/emp_pass_reset.php');
                echo "<text>Checked for testPass</text>";
            }
            else{
                header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/emp_main.php' );
            }
        }else{
            echo '<p style="color:red">incorrect username and password</p>';
        } 
    }
?>
<html>
    <form method="POST">
        <text>username:</text>
        <input type = "text" name="username"> <br>
        <text>password:</text>
        <input type = "text" name="password"> <br>
        <input type = "submit" value = "login"> 
    </form>
</html>