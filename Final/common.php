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
function changeCustPass($user, $newPassword) {
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("UPDATE customer SET password = sha2(:password,256) WHERE username = :user;");
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
function getProductOrder($pid){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT * FROM (SELECT o_id, product.p_id, name, description, order_item.price, cat_name, quantity, image FROM product JOIN order_item ON product.p_id = order_item.p_id) AS t1 NATURAL JOIN orders WHERE p_id = :pid and c_id = :cid");
        $statement -> bindParam(":pid", $pid);
        $statement -> bindParam(":cid", $_SESSION["c_id"]);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return $row;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function getProductCart($pid){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT * FROM product NATURAL JOIN cart_item WHERE p_id = :pid and c_id = :cid");
        $statement -> bindParam(":pid", $pid);
        $statement -> bindParam(":cid", $_SESSION["c_id"]);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return $row;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function getProductsOrder($products){
    $output = [];
    for($i = 0; $i < count( $products); $i++){
        $output[$i] = [];
        $product = getProductOrder($products[$i]["p_id"]);
        $output[$i]["p_id"] = $product['p_id'];
        $output[$i]["name"] = $product['name'];
        $output[$i]["desc"] = $product['description'];
        $output[$i]["price"] = $product['price'];
        $output[$i]["image"] = $product['image'];
        $output[$i]["cat_name"] = $product['cat_name'];
        $output[$i]["quantity"] = $product['quantity'];
        $output[$i]["total"] = $product['total'];
        $output[$i]["status"] = $product['status'];
    }
    return $output;
}
function getProductsCart($products){
    $output = [];
    for($i = 0; $i < count( $products); $i++){
        $output[$i] = [];
        $product = getProductCart($products[$i]["p_id"]);
        $output[$i]["p_id"] = $product['p_id'];
        $output[$i]["name"] = $product['name'];
        $output[$i]["desc"] = $product['description'];
        $output[$i]["price"] = $product['price'];
        $output[$i]["stock"] = $product['stock'];
        $output[$i]["adv_thres"] = $product['adv_thres'];
        $output[$i]["image"] = $product['image'];
        $output[$i]["discontinued"] = $product['discontinued'];
        $output[$i]["cat_name"] = $product['cat_name'];
        $output[$i]["quantity"] = $product['quantity'];
    }
    return $output;
}
function authenticateOrder($oid){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT count(*) FROM orders WHERE o_id = :o_id and c_id = :cid;");
        $statement -> bindParam(":o_id", $oid);
        $statement -> bindParam(":cid", $_SESSION["c_id"]);
        $result = $statement -> execute();
        $row=$statement -> fetch();
        $dbh = null;
        return $row;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
}
function getOrder($oid){
    try {
        $dbh = connectDB();
        $statement = $dbh -> prepare("SELECT p_id FROM orders NATURAL JOIN order_item WHERE o_id = :o_id;");
        $statement -> bindParam(":o_id", $oid);
        $result = $statement -> execute();
        $products=$statement -> fetchAll();
        $dbh = null;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
    return getProductsOrder($products);
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
    return getProductsCart($products);
}
function printCart($products){
    foreach($products as $product){
        echo "<h3>",$product['quantity'], "x ", $product['name'], " at $", $product["price"], "</h3>";
    }
}
function printOrder($products){
    foreach($products as $product){
        echo "<h3>",$product['quantity'], "x ", $product['name'], " at $", $product["price"], "</h3>";
    }
    echo "<h3>Total: $", $products[0]["total"],"<h3>";
    echo "<h3>Status: ", $products[0]["status"],"<h3>";
}
function checkout(){
    try {
        $dbh = connectDB();
        $statement1 = $dbh -> prepare("
            CALL checkout(1, @id, @pid);");
        $result1 = $statement1 -> execute();
        $statement2 = $dbh -> prepare("SELECT * from orders where o_id = last_insert_id();");
        $result2 = $statement2 -> execute();
        $info = $statement2 -> fetch();
        $dbh = null;
    }catch (PDOException $e) {
        print "Error!" . $e -> getMessage() . "<br/>";
        die();
    }
    $output = [];
    $output["o_id"] = $info["o_id"];
    $output["date"] = $info["date"];
    $output["total"] = $info["total"];
    return $output;
}
?>