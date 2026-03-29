<?php 
include __DIR__ . '/nav.php';

// Filters
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

// SQL query with filters
$sql = "SELECT productName, category, productImage, MIN(productID) AS productID
        FROM products 
        WHERE 1";

if ($category != '') {
    $sql .= " AND category = '$category'";
}

if ($status != '') {
    $sql .= " AND status = '$status'";
}

// Group by productName to collapse duplicates
$sql .= " GROUP BY productName";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Marketplace</title>
    <link rel="stylesheet" href="/TechCycle/UI_screens/styles.css">
</head>

<body>

    <div class="container">
        <main>

            <h1>Available Parts</h1>

            <!-- Filters -->
            <form method="GET" class="form">
                <label>Filter by Category:</label>
                <select name="category" style="border-radius: 5px;">
                    <option value="">All</option>
                    <option value="RAM" <?= $category=='RAM'?'selected':'' ?>>RAM</option>
                    <option value="Storage" <?= $category=='Storage'?'selected':'' ?>>Storage</option>
                    <option value="GPU" <?= $category=='GPU'?'selected':'' ?>>GPU</option>
                    <option value="CPU" <?= $category=='CPU'?'selected':'' ?>>CPU</option>
                    <option value="Motherboard" <?= $category=='Motherboard'?'selected':'' ?>>Motherboard</option>
                    <option value="Electronic components" <?= $category=='Electronic components'?'selected':'' ?>>Electronic components</option>
                </select>

                <button type="submit" class="button primary">Apply Filters</button>
            </form>

            <!-- Product Grid -->
            <div class="grid">
                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php
                    $productName = $row['productName'];

                    // Count total items with same name
                    $stockSQL = "SELECT COUNT(*) AS totalStock 
                                 FROM products 
                                 WHERE productName = '$productName'
                                 AND status = 'Available'";
                    $stockResult = $conn->query($stockSQL);
                    $stockData = $stockResult->fetch_assoc();
                    $totalStock = $stockData['totalStock'];

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

                    // Final available stock
                    $availableStock = $totalStock - $totalOrdered;
                    ?>

                    <div class="card">
                        <img src="/TechCycle/<?php echo $row['productImage']; ?>">

                        <h3><?php echo $productName; ?></h3>

                        <p class="muted" style="padding-bottom: 5px;">
                            Category: <?php echo $row['category']; ?><br>
                            Stock Available: <?php echo $availableStock; ?>
                        </p>

                        <a href="productInfoScreen.php?id=<?php echo $row['productID']; ?>" class="button primary">
                            View Cart
                        </a>
                    </div>

                <?php endwhile; ?>
            </div>

        </main>
    </div>

</body>

</html>

<?php include 'footer.php'; ?>