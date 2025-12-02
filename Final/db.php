<?php
function connectDB()
{
    $config = parse_ini_file("../../db.ini");
    $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
function authenticateCust($user, $passwd) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT count(*) FROM customer " . "where username = :username and password = sha2(:passwd,256) ");
        $statement -> bindParam(":username", $user);
        $statement -> bindParam(":passwd", $passwd);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return $row[0];
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function authenticateEmp($user, $passwd) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT count(*) FROM employee " . "where username = :username and password = sha2(:passwd,256) ");
        $statement -> bindParam(":username", $user);
        $statement -> bindParam(":passwd", $passwd);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return $row[0];
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function addCustomer($user, $password, $email, $firstname, $lastname, $address) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("INSERT INTO customer(username, password, email, first_name, last_name, address) VALUES 
(:user, sha2(:password, 256), :email, :firstname, :lastname, :address);");
        $statement -> bindParam(":user", $user);
        $statement -> bindParam(":password", $password);
        $statement -> bindParam(":email", $email);
        $statement -> bindParam(":firstname", $firstname);
        $statement -> bindParam(":lastname", $lastname);
        $statement -> bindParam(":address", $address);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function changeEmpPass($user, $newPassword) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("UPDATE employee SET password = sha2(:password,256) WHERE username = :user;");
        $statement -> bindParam(":password", $newPassword);
        $statement -> bindParam(":user", $user);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
?>