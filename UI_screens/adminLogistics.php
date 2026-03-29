<?php 
include __DIR__ . '/nav.php';


// Fetch logistics items
$result = $conn->query("SELECT * FROM products WHERE pickupStatus = 'collected'");
?>

<section class="hero product-marketplace" style="
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    justify-content: center; 
    text-align: center; 
    padding: 20px 20px; 
    background: #f8f9fa; 
    border-radius: 12px; 
    margin: 20px 0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
"> 
    <h2 style="
        font-size: 2.5rem; 
        color: #6cc551; 
        margin-bottom: 10px;
    ">E-Waste Logistics</h2> 
    
    <p style="
        font-size: 1.1rem; 
        color: #5a6268; 
        max-width: 600px; 
        margin-bottom: 5px;
    ">Manage and monitor all e-waste items efficiently in one centralized dashboard.</p> 
    
    <a href="createLogistics.php" class="button primary" style="
        text-decoration: none; 
        padding: 12px 28px; 
        background-color: #6cc551; 
        color: white; 
        border-radius: 6px; 
        font-weight: bold;
        transition: background-color 0.3s ease;
    ">Create Listing</a> 
</section>

<main id="ecoswap">
    <section class="card">
        <div class="grid">

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    
                    echo '<img src="../' . htmlspecialchars($row['image_path']) . '" class="product-img">';
                    
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    
                    echo '<p><strong>Status:</strong> ' . $row['status'] . '</p>';

                    echo '<a href="updateLogistics.php?id=' . $row['id'] . '" class="button primary">Edit</a>';
                    
                    echo '</div>';
                }
            } else {
                echo '<p>No logistics items found.</p>';
            }
            ?>

        </div>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>