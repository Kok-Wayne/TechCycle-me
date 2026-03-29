<?php
include 'nav.php';
include __DIR__ . '/../php_files/dbConnection.php';

$userID = $_SESSION['user_id'];

// DELETE ITEM
if (isset($_POST['deleteItem'])) {
    $orderID = $_POST['orderID'];

    // Get order info
    $getData = "SELECT o.quantity, p.productName
                FROM orders o
                JOIN products p ON o.productID = p.productID
                WHERE o.orderID = '$orderID'";
    $res = $conn->query($getData);
    $data = $res->fetch_assoc();

    $quantity = $data['quantity'];
    $productName = $data['productName'];

    // Restore stock
    $restoreSQL = "UPDATE products 
                   SET status = 'Available'
                   WHERE productName = '$productName'
                   AND status = 'Unavailable'
                   LIMIT $quantity";
    $conn->query($restoreSQL);

    // Delete order
    $deleteSQL = "DELETE FROM orders WHERE orderID = '$orderID'";
    $conn->query($deleteSQL);

    header("Location: cartScreen.php");
    exit;
}

// INCREASE / DECREASE QUANTITY
if (isset($_POST['increaseQty']) || isset($_POST['decreaseQty'])) {

    $orderID = $_POST['orderID'];

    // Get current quantity + product name
    $getData = "SELECT o.quantity, p.productName
                FROM orders o
                JOIN products p ON o.productID = p.productID
                WHERE o.orderID = '$orderID'";
    $res = $conn->query($getData);
    $data = $res->fetch_assoc();

    $currentQty = $data['quantity'];
    $productName = $data['productName'];

    if (isset($_POST['increaseQty'])) {
        $newQty = $currentQty + 1;
    } else {
        $newQty = $currentQty - 1;
    }

    if ($newQty < 1) $newQty = 1;

    // STOCK CALCULATION
    $stockSQL = "SELECT COUNT(*) AS totalStock
                 FROM products
                 WHERE productName = '$productName'";
    $stockRes = $conn->query($stockSQL);
    $totalStock = $stockRes->fetch_assoc()['totalStock'];

    $orderSQL = "SELECT SUM(quantity) AS totalOrdered
                 FROM orders
                 WHERE productID IN (
                     SELECT productID FROM products WHERE productName = '$productName'
                 )
                 AND status IN ('Cart','Ordered','Preparing','Shipped')
                 AND orderID != '$orderID'";
    $orderRes = $conn->query($orderSQL);
    $totalOrdered = $orderRes->fetch_assoc()['totalOrdered'] ?? 0;

    $availableStock = $totalStock - $totalOrdered;

    // Get current quantity first
    $currentSQL = "SELECT quantity, productID FROM orders WHERE orderID = '$orderID'";
    $currentRes = $conn->query($currentSQL);
    $currentData = $currentRes->fetch_assoc();

    $oldQty = $currentData['quantity'];
    $productID = $currentData['productID'];

    // Get product name
    $nameSQL = "SELECT productName FROM products WHERE productID = '$productID'";
    $nameRes = $conn->query($nameSQL);
    $nameData = $nameRes->fetch_assoc();
    $productName = $nameData['productName'];

    $difference = $newQty - $oldQty;
    if ($newQty <= $availableStock) {
        $updateSQL = "UPDATE orders 
                  SET quantity = '$newQty' 
                  WHERE orderID = '$orderID'";
        $conn->query($updateSQL);
    }

    // If quantity increased → reduce stock
    if ($difference > 0) {
        $updateStockSQL = "UPDATE products 
                       SET status = 'Unavailable'
                       WHERE productName = '$productName'
                       AND status = 'Available'
                       LIMIT $difference";
        $conn->query($updateStockSQL);
    }

    // If quantity decreased → restore stock
    if ($difference < 0) {
        $restoreSQL = "UPDATE products 
                   SET status = 'Available'
                   WHERE productName = '$productName'
                   AND status = 'Unavailable'
                   LIMIT " . abs($difference);
        $conn->query($restoreSQL);
    }

    header("Location: cartScreen.php");
    exit;
}

// GET CART ITEMS
$sql = "SELECT o.orderID, o.quantity, p.productName, p.productImage
        FROM orders o
        JOIN products p ON o.productID = p.productID
        WHERE o.userID = '$userID' AND o.status = 'Cart'";

$result = $conn->query($sql);
?>

<body>
    <div class="container cart-page">
        <main>
            <h2>Your Cart</h2>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="cart-container">
                        <div class="cart-tab">
                            <img src="/TechCycle/<?php echo $row['productImage']; ?>" class="cart-img">
                            <div class="cart-info">
                                <h3><?php echo $row['productName']; ?></h3>
                                <form method="POST" class="qty-form">
                                    <input type="hidden" name="orderID" value="<?php echo $row['orderID']; ?>">
                                    <div class="qty-box">
                                        <button type="submit" name="decreaseQty" class="qty-btn">-</button>
                                        <input type="number"
                                            value="<?php echo $row['quantity']; ?>"
                                            class="qty-input"
                                            readonly>
                                        <button type="submit" name="increaseQty" class="qty-btn">+</button>
                                    </div>
                                </form>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="orderID" value="<?php echo $row['orderID']; ?>">
                                <button type="submit" name="deleteItem" class="delete-btn">X</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>