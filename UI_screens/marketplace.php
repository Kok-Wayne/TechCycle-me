<?php include 'nav.php'; ?>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "techcycle_database");

// Filters
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

// SQL query with filters
$sql = "SELECT * FROM products WHERE 1";

if ($category != '') {
    $sql .= " AND category = '$category'";
}

if ($status != '') {
    $sql .= " AND status = '$status'";
}

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
                <option value="RAM">RAM</option>
                <option value="Storage">Storage</option>
                <option value="GPU">GPU</option>
                <option value="CPU">CPU</option>
                <option value="Motherboard">Motherboard</option>
                <option value="Electronic components">Electronic components</option>
            </select>

            <button type="submit" class="button primary">Apply Filters</button>
        </form>

        <!-- Product Grid -->
        <div class="grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="/TechCycle/<?php echo $row['productImage']; ?>">

                    <h3><?php echo $row['productName']; ?></h3>

                    <p class="muted">
                        Category: <?php echo $row['category']; ?><br>
                        Status: <?php echo $row['status']; ?>
                    </p>

                    <a href="productPage.php?id=<?php echo $row['productID']; ?>" 
                       class="button primary">
                       View Part
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

    </main>
</div>

</body>
</html>

<?php include 'footer.php'; ?>