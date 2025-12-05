<?php
    session_start();
    require "common.php";

    if(!isset($_SESSION["username"])){
       header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }
    if(isset($_POST["logout"])){
       header( 'Location: https://classdb.it.mtu.edu/~jopking/Final/main.php' );
    }

$emp_id = $_SESSION["e_id"];
$emp_user = $_SESSION["username"];
$emp_email = $_SESSION["email"];

//actions
$message = "";

if (isset($_POST["restock"])) {
    $pid = $_POST["product_id"];
    $amount = intval($_POST["amount"]);
    $product = getProduct($pid);
    $stock = $product["stock"];
    $new_stock = $product["stock"] + $amount;

    try {
        $dbh = connectDB();
        $dbh->beginTransaction();

        // update product stock
        $stmt = $dbh->prepare("UPDATE product SET stock = stock + :amt WHERE p_id = :pid");
        $stmt->bindParam(":amt", var: $amount);
        $stmt->bindParam(":pid", var: $pid);
        $stmt->execute();

        //log_product_update(pid, action_type, old_price, new_price, old_stock, new_stock, e_id, c_id, o_id);
        // add history
        $stmt = $dbh->prepare(query: "
            CALL log_product_update(:pid, 'UPDATE', null, null, :old_stock, :new_stock, :eid, null, null);
        ");
        $stmt->bindParam(":pid", $pid);
        $stmt->bindParam(":old_stock", $stock);
        $stmt->bindParam(":new_stock", $new_stock);
        $stmt->bindParam(":eid", $emp_id);
        $stmt->execute();

        $dbh->commit();
        $dbh = null;

        $message = "Product $pid restocked by $amount.";
    } catch (PDOException $e) {
        $dbh->rollBack();
        $message = "Error restocking product.";
    }
}

if (isset($_POST["changePrice"])) {

    $pid = $_POST["product_id"];
    $newprice = floatval($_POST["newprice"]);
    $product = getProduct($pid);
    $oldprice = $product["price"];

    try {
        $dbh = connectDB();
        $dbh->beginTransaction();

        $stmt = $dbh->prepare("UPDATE product SET price = :p WHERE p_id = :pid");
        $stmt->bindParam(":p", $newprice);
        $stmt->bindParam(":pid", $pid);
        $stmt->execute();

        //log_product_update(pid, action_type, old_price, new_price, old_stock, new_stock, e_id, c_id, o_id);
        $stmt = $dbh->prepare("
            CALL log_product_update(:pid, 'UPDATE', :oldp, :newp, null, null, :eid, null, null);
        ");
        $stmt->bindParam(":pid", $pid);
        $stmt->bindParam(":oldp", $oldprice);
        $stmt->bindParam(":newp", $newprice);
        $stmt->bindParam(":eid", $emp_id);
        $stmt->execute();

        $dbh->commit();
        $dbh = null;

        $message = "Price updated from $old to $newprice for product $pid.";

    } catch (PDOException $e) {
        $dbh->rollBack();
        $message = "Error changing price.";
    }
}

// STOCK HISTORY
$stock_history = [];
if (isset($_POST["showStockHistory"])) {

    $pid = $_POST["product_id"];

    try {
        $dbh = connectDB();
        $stmt = $dbh->prepare("
            SELECT stock_after - stock_before as change_amount, stock_after, operation, time, e_id, o_id FROM stock_history WHERE p_id = :pid ORDER BY time DESC;
        ");
        $stmt->bindParam(":pid", $pid);
        $stmt->execute();
        $stock_history = $stmt->fetchAll();
        $dbh = null;
    } catch (PDOException $e) {
        $message = "Error retrieving stock history.";
    }
}

// PRICE HISTORY
$price_history = [];
if (isset($_POST["showPriceHistory"])) {

    $pid = $_POST["product_id"];

    try {
        $dbh = connectDB();
        $stmt = $dbh->prepare("
            SELECT price_before, price_after, time, e_id FROM price_history WHERE p_id = :pid ORDER BY time DESC;
        ");
        $stmt->bindParam(":pid", $pid);
        $stmt->execute();
        $price_history = $stmt->fetchAll();
        $dbh = null;
    } catch (PDOException $e) {
        $message = "Error retrieving price history.";
    }
}

?>
<html>
<body>
<h1>Welcome to The Store, where you Work™</h1>
<h2>We don't pay you enough, but you Work™ anyway!</h2>

<?php if ($message !== "") echo "<p><b>$message</b></p>"; ?>

<hr>

<h2>Restock Product</h2>
<form method="POST">
    Product ID: <input type="number" name="product_id" required>
    Add Amount: <input type="number" name="amount" required>
    <input type="submit" name="restock" value="Restock">
</form>

<hr>

<h2>Change Product Price</h2>
<form method="POST">
    Product ID: <input type="number" name="product_id" required>
    New Price: <input type="text" name="newprice" required>
    <input type="submit" name="changePrice" value="Change Price">
</form>

<hr>

<h2>Stock History</h2>
<form method="POST">
    Product ID: <input type="number" name="product_id" required>
    <input type="submit" name="showStockHistory" value="View Stock History">
</form>

<?php
if (!empty($stock_history)) {
    $product = getProduct($_POST["product_id"]);
    echo"<h4>Current Item: #", $_POST["product_id"], " - ", $product['name'],"</h4>";
    echo "<table border='1'><tr><th>Change</th><th>New Stock</th><th>Reason</th><th>Time</th><th>Employee</th><th>Order #</th></tr>";
    foreach ($stock_history as $s) {
        echo "<tr><td>{$s['change_amount']}</td><td>{$s['stock_after']}</td><td>{$s['operation']}</td><td>{$s['time']}</td><td>{$s['e_id']}</td><td>{$s['o_id']}</td></tr>";
    }
    echo "</table>";
}
?>

<hr>

<h2>Price History</h2>
<form method="POST">
    Product ID: <input type="number" name="product_id" required>
    <input type="submit" name="showPriceHistory" value="View Price History">
</form>

<?php
if (!empty($price_history)) {
    //html skillz flexxxxxxx
    $product = getProduct($_POST["product_id"]);
    echo"<h4>Current Item: #", $_POST["product_id"], " - ", $product['name'],"</h4>";
    echo "<table border='1'><tr><th>Old Price</th><th>New Price</th><th>Time</th><th>Employee</th></tr>";
    foreach ($price_history as $p) {
        echo "<tr><td>{$p['price_before']}</td><td>{$p['price_after']}</td><td>{$p['time']}</td><td>{$p['e_id']}</td></tr>";
    }
    echo "</table>";
}
?>

<hr>
<form method="POST" action="main.php">
    <input type="submit" value="Logout">
</form>

</body>
</html>
