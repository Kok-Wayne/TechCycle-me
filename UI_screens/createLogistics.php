<?php
include __DIR__ . '/nav.php';

// Fetch ONLY collected products (latest first)
$products = $conn->query("
    SELECT * FROM products 
    WHERE pickupStatus = 'collected'
    ORDER BY createdAt DESC
");

?>

<div style="
    max-width: 600px;
    margin: 40px auto;
    padding: 25px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
">

    <h2 style="
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    ">
        Create Logistics Item
    </h2>

    <form action="php_files/createLogisticsProcess.php" method="POST" enctype="multipart/form-data">

        <!-- SELECT PRODUCT -->
        <label style="font-weight: bold; display:block; margin-top: 10px;">Select Product:</label>
        <select id="productSelect" name="productID" required
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">

            <option value="">Select Collected Product</option>

            <?php while ($p = $products->fetch_assoc()): ?>
                <option value="<?= $p['productID'] ?>" data-name="<?= htmlspecialchars($p['productName']) ?>"
                    data-desc="<?= htmlspecialchars($p['productDesc']) ?>">
                    <?= htmlspecialchars($p['productName']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- NAME -->
        <label style="font-weight: bold; display:block; margin-top: 15px;">Product Name:</label>
        <input type="text" name="name" id="name"
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">

        <!-- DESCRIPTION -->
        <label style="font-weight: bold; display:block; margin-top: 15px;">Description:</label>
        <textarea name="description" id="description"
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px; height:100px;"></textarea>

        <!-- STATUS -->
        <label style="font-weight: bold; display:block; margin-top: 15px;">Status:</label>
        <select name="status"
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">
            <option value="unlisted">Unlisted</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
        </select>

        <!-- IMAGE -->
        <label style="font-weight: bold; display:block; margin-top: 15px;">Upload Image:</label>
        <input type="file" name="image" required
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px; background:#f9f9f9;">

        <!-- BUTTON -->
        <button type="submit" style="
            width:100%;
            margin-top:20px;
            padding:12px;
            background:#28a745;
            color:white;
            border:none;
            border-radius:8px;
            font-size:16px;
            cursor:pointer;
            transition:0.3s;
        " onmouseover="this.style.background='#6cc551'" onmouseout="this.style.background='#447604'">
            Add Listing to marketplace
        </button>

    </form>

</div>
<script>
    document.getElementById('productSelect').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];

        document.getElementById('name').value = selected.getAttribute('data-name') || '';
        document.getElementById('description').value = selected.getAttribute('data-desc') || '';
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>