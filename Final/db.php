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
?>