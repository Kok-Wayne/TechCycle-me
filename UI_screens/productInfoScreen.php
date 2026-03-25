<?php include 'nav.php'; ?>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "techcycle_database");

$productID = $_GET['id'];
$userID = $_SESSION['userID'];

// Get product info
$sql = "SELECT * FROM products WHERE productID = $productID";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

// Place order
if (isset($_POST['placeOrder'])) {
    $quantity = $_POST['quantity'];

    $orderSQL = "INSERT INTO orders (userID, productID, status, quantity)
                 VALUES ('$userID', '$productID', 'Ordered', '$quantity')";

    $conn->query($orderSQL);
    echo "<p>Order placed successfully!</p>";
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
                <p><strong>Status:</strong> <?php echo $product['status']; ?></p>

                <form method="POST">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" class="qty-input">

                    <br><br>

                    <button type="submit" name="placeOrder" class="cta">Place Order</button>
                </form>
            </div>

        </main>
    </div>

</body>

</html>

<?php include 'footer.php'; ?>