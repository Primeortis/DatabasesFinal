<?php
    session_start();
    require "common.php";
    require "db.php";

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

    try {
        $dbh = connectDB();
        $dbh->beginTransaction();

        // update product stock
        $stmt = $dbh->prepare("UPDATE product SET stock = stock + :amt WHERE p_id = :pid");
        $stmt->bindParam(":amt", $amount);
        $stmt->bindParam(":pid", $pid);
        $stmt->execute();

        // add history
        $stmt = $dbh->prepare("
            INSERT INTO stock_history(p_id, change_amount, reason, change_time, employee_id)
            VALUES(:pid, :amt, 'restock', NOW(), :eid)
        ");
        $stmt->bindParam(":pid", $pid);
        $stmt->bindParam(":amt", $amount);
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

    try {
        $dbh = connectDB();

        // get old price
        $stmt = $dbh->prepare("SELECT price FROM product WHERE p_id = :pid");
        $stmt->bindParam(":pid", $pid);
        $stmt->execute();
        $old = $stmt->fetchColumn();

        $dbh->beginTransaction();

        $stmt = $dbh->prepare("UPDATE product SET price = :p WHERE p_id = :pid");
        $stmt->bindParam(":p", $newprice);
        $stmt->bindParam(":pid", $pid);
        $stmt->execute();

        $stmt = $dbh->prepare("
            INSERT INTO price_history(p_id, old_price, new_price, change_time, employee_id)
            VALUES(:pid, :oldp, :newp, NOW(), :eid)
        ");
        $stmt->bindParam(":pid", $pid);
        $stmt->bindParam(":oldp", $old);
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
            SELECT change_amount, reason, change_time, employee_id
            FROM stock_history
            WHERE p_id = :pid
            ORDER BY change_time DESC
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
            SELECT old_price, new_price, change_time, employee_id
            FROM price_history
            WHERE p_id = :pid
            ORDER BY change_time DESC
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
    echo "<table border='1'><tr><th>Change</th><th>Reason</th><th>Time</th><th>Employee</th></tr>";
    foreach ($stock_history as $s) {
        echo "<tr><td>{$s['change_amount']}</td><td>{$s['reason']}</td><td>{$s['change_time']}</td><td>{$s['employee_id']}</td></tr>";
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
    echo "<table border='1'><tr><th>Old Price</th><th>New Price</th><th>Time</th><th>Employee</th></tr>";
    foreach ($price_history as $p) {
        echo "<tr><td>{$p['old_price']}</td><td>{$p['new_price']}</td><td>{$p['change_time']}</td><td>{$p['employee_id']}</td></tr>";
    }
    echo "</table>";
}
?>

<hr>
<form method="POST" action="emp_logout.php">
    <input type="submit" value="Logout">
</form>

</body>
</html>
