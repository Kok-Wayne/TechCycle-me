<?php include __DIR__ . '/nav.php' ?>

<section class="hero product-marketplace">
    <h2>Eco-Swap Marketplace</h2>
    <p>Trade your items with others in the community.</p>
    <a href="createProduct.html" class="button primary">Create Listing</a>
</section>


<main id="ecoswap">
    <section class="card">
        <div class="grid">
            <!-- <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Product Image" class="product-img">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '<a href="productDetails.php?id=' . $row['id'] . '" class="button ghost">View Details</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available yet.</p>';
            }
            ?> -->
            <div class="card">
                <img src="images/sample1.jpg" alt="Product Image" class="product-img">
                <h3>Wooden Chair</h3>
                <p>A sturdy wooden chair in good condition.</p>
                <a href="productDetails.php?id=1" class="button ghost">View Details</a>
            </div>

            <div class="card">
                <img src="images/sample2.jpg" alt="Product Image" class="product-img">
                <h3>Mountain Bike</h3>
                <p>A well-maintained mountain bike, perfect for off-road trails.</p>
                <a href="productDetails.php?id=2" class="button ghost">View Details</a>
            </div>

            <div class="card">
                <img src="images/sample3.jpg" alt="Product Image" class="product-img">
                <h3>Kitchen Table</h3>
                <p>A spacious kitchen table that can seat six people.</p>
                <a href="productDetails.php?id=3" class="button ghost">View Details</a>
            </div>

            <div class="card">
                <img src="images/sample4.jpg" alt="Product Image" class="product-img">
                <h3>Bookshelf</h3>
                <p>A tall bookshelf with multiple shelves for storage.</p>
                <a href="productDetails.php?id=4" class="button ghost">View Details</a>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/footer.php' ?>