<?php
include 'nav.php';

$productID = $_GET['id'];
$userID = $_SESSION['user_id'];

// Get product info
$sql = "SELECT * FROM products WHERE productID = $productID";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

$productName = $product['productName'];

$productName = $product['productName'];

$category = $product['category'];

// Get related products in same category (excluding current product)
$relatedSQL = "SELECT productName, MIN(productID) as productID, productImage
               FROM products
               WHERE category = '$category'
               AND productName != '$productName'
               GROUP BY productName";

$relatedResult = $conn->query($relatedSQL);
$relatedCount = $relatedResult->num_rows;

// Count how many items exist with same name
$stockSQL = "SELECT COUNT(*) AS availableStock
             FROM products 
             WHERE productName = '$productName'
             AND status = 'Available'";
$stockResult = $conn->query($stockSQL);
$stockData = $stockResult->fetch_assoc();
$availableStock = $stockData['availableStock'];

// Calculate total ordered
$orderSQL = "SELECT SUM(quantity) AS totalOrdered
             FROM orders
             WHERE productID IN (
                 SELECT productID FROM products WHERE productName = '$productName'
             )
             AND status IN ('Ordered','Preparing','Shipped')";

$orderResult = $conn->query($orderSQL);
$orderData = $orderResult->fetch_assoc();
$totalOrdered = $orderData['totalOrdered'] ?? 0;

// Place order
if (isset($_POST['placeOrder'])) {
    $quantity = (int) $_POST['quantity'];

    // Recalculate stock AGAIN (important to prevent cheating)
    $stockSQL = "SELECT COUNT(*) AS availableStock
             FROM products 
             WHERE productName = '$productName'
             AND status = 'Available'";
    $stockResult = $conn->query($stockSQL);
    $stockData = $stockResult->fetch_assoc();
    $availableStock = $stockData['availableStock'];

    $orderSQL = "SELECT SUM(quantity) AS totalOrdered
                 FROM orders
                 WHERE productID IN (
                     SELECT productID FROM products WHERE productName = '$productName'
                 )
                 AND status IN ('Cart','Ordered','Preparing','Shipped')";
    $orderResult = $conn->query($orderSQL);
    $orderData = $orderResult->fetch_assoc();
    $totalOrdered = $orderData['totalOrdered'] ?? 0;

    if ($availableStock <= 0) {
        echo "<p>Out of stock.</p>";
    } elseif ($quantity > $availableStock) {
        echo "<p>Only $availableStock items available.</p>";
    } elseif ($quantity > 100) {
        echo "<p>Maximum order is 100.</p>";
    } elseif ($quantity < 1) {
        echo "<p>Invalid quantity.</p>";
    } else {
        // Insert order
        $orderSQL = "INSERT INTO orders (userID, productID, status, quantity)
             VALUES ('$userID', '$productID', 'Cart', '$quantity')";
        $conn->query($orderSQL);

        // Set selected number of same products to Unavailable
        $updateStockSQL = "UPDATE products 
                   SET status = 'Unavailable'
                   WHERE productName = '$productName'
                   AND status = 'Available'
                   LIMIT $quantity";

        $conn->query($updateStockSQL);
        echo "<p>Item added to cart!</p>";
        header("Refresh:1");
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/TechCycle/UI_screens/styles.css">
</head>

<body>
    <div class="container">
        <main>
            <div class="product-card">
                <h2><?php echo $product['productName']; ?></h2>
                <img src="/TechCycle/<?php echo $product['productImage']; ?>" class="product-image">
                <p><strong>Category:</strong> <?php echo $product['category']; ?></p>
                <p><strong>Status:</strong>
                    <?php
                    if ($availableStock > 0) {
                        echo "Available";
                    } else {
                        echo "Unavailable";
                    }
                    ?>
                </p>
                <p><strong>Stock Available:</strong> <?php echo $availableStock; ?></p>
                <form method="POST">
                    <label>Quantity:</label>
                    <input type="number"
                        name="quantity"
                        value="1"
                        min="1"
                        max="<?php echo min(100, $availableStock); ?>"
                        class="qty-input">
                    <br><br>
                    <button type="submit" name="placeOrder" class="cta">Add To Cart</button>
                </form>
            </div>
        </main>
    </div>

    <h2 style="margin-top: 50px; padding-left: 20px;">Related Products</h2>

    <?php if ($relatedCount > 0): ?>
        <div class="related-wrapper">
            <button class="arrow left" onclick="scrollRelated(-1)">❮</button>

            <div class="related-slider" id="relatedSlider">
                <?php while ($related = $relatedResult->fetch_assoc()): ?>
                    <div class="related-card">
                        <img src="/TechCycle/<?php echo $related['productImage']; ?>">
                        <h4><?php echo $related['productName']; ?></h4>

                        <a href="productInfoScreen.php?id=<?php echo $related['productID']; ?>"
                            class="button primary">
                            View
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

            <button class="arrow right" onclick="scrollRelated(1)">❯</button>
        </div>
    <?php else: ?>
        <p class="muted" style="padding-bottom: 50px; padding-top: 20px;">No related products found.</p>
    <?php endif; ?>
</body>

<script>
    function scrollRelated(direction) {
        const slider = document.getElementById("relatedSlider");
        slider.scrollLeft += direction * 220;
    }
</script>

</html>
<?php include 'footer.php'; ?>