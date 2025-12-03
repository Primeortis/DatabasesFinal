<?php
require 'db.php';
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
function setCustomerSession($user) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT * FROM customer where username = :username");
        $statement -> bindParam(":username", $user);
        $result = $statement -> execute();
        $info=$statement -> fetch();
        $dbh = null;
        
        $_SESSION["c_id"] = $info[0];
        $_SESSION["first"] = $info[1];
        $_SESSION["last"] = $info[2];
        $_SESSION["email"] = $info[3];
        $_SESSION["username"] = $info[4];
        $_SESSION["address"] = $info[6];
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
function setEmployeeSession($user) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT * FROM employee where username = :username");
        $statement -> bindParam(":username", $user);
        $result = $statement -> execute();
        $info=$statement -> fetch();
        $dbh = null;
        
        $_SESSION["e_id"] = $info[0];
        $_SESSION["email"] = $info[1];
        $_SESSION["username"] = $info[2];
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
function getProduct($pid){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT * FROM product WHERE p_id = :pid");
        $statement -> bindParam(":pid", $pid);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return $row;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function getProducts($products){
    $output = [];
    for($i = 0; $i < count( $products); $i++){
        $output[$i] = [];
        $product = getProduct($products[$i]["p_id"]);
        $output[$i]["p_id"] = $product['p_id'];
        $output[$i]["name"] = $product['name'];
        $output[$i]["desc"] = $product['description'];
        $output[$i]["price"] = $product['price'];
        $output[$i]["stock"] = $product['stock'];
        $output[$i]["adv_thres"] = $product['adv_thres'];
        $output[$i]["image"] = $product['image'];
        $output[$i]["discontinued"] = $product['discontinued'];
        $output[$i]["cat_name"] = $product['cat_name'];
    }
    return $output;
}
function getOrder($oid){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT p_id FROM orders NATURAL JOIN order_item WHERE o_id = :oid;");
        $statement -> bindParam(":oid", $oid);
        $result = $statement -> execute();
        $products=$statement -> fetchAll();
        $dbh = null;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
    return getProducts($products);
}
function getCart(){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT p_id FROM cart NATURAL JOIN cart_item WHERE c_id = :cid");
        $c_id = $_SESSION['c_id'];
        $statement -> bindParam(":cid",$c_id);
        $result = $statement -> execute();
        $products=$statement -> fetchAll();
        $dbh = null;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
    return getProducts($products);
}
function printProducts($products){
    foreach($products as $product){
        var_dump($product);
    echo"</br>";
    }
}
?>