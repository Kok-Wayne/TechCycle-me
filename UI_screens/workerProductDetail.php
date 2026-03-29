<?php
    include __DIR__ . '/nav.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
        header("Location: login.php");
        exit();
    }

    if (!isset($_GET['id'])) {
        header("Location: workerCollectionTasks.php");
        exit();
    }

    $workerID  = (int) $_SESSION['user_id'];
    $productID = (int) $_GET['id'];

    // Fetch product + recycling centre details
    $stmt = $conn->prepare(
        "SELECT p.productID, p.productName, p.productDesc, p.productCategory,
                p.productImage, p.pickupStatus, p.collectedByWorkerID, p.collectedAt,
                rc.centreName, rc.centreAddress, rc.centreMapLink
        FROM products p
        LEFT JOIN recycling_centre rc ON rc.centreID = p.centreID
        WHERE p.productID = ?
        LIMIT 1"
    );
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        header("Location: workerCollectionTasks.php");
        exit();
    }

    $isCollected = ($product['pickupStatus'] === 'collected');
?>

<div class="worker-task-page">
    <main id="content">
        <div class="detail-wrapper">

            <!-- Back link -->
            <a href="workerCollectionTasks.php" class="detail-back">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Collection Tasks
            </a>

            <!-- Product image -->
            <div class="detail-img-wrap">
                <?php if (!empty($product['productImage'])): ?>
                    <img src="/TechCycle/<?= htmlspecialchars($product['productImage']) ?>" alt="<?= htmlspecialchars($product['productName']) ?>">
                <?php else: ?>
                    <div class="detail-img-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info card -->
            <div class="detail-card">
                <h1 class="detail-product-name"><?= htmlspecialchars($product['productName']) ?></h1>
                <span class="detail-category"><?= htmlspecialchars($product['productCategory']) ?></span>

                <!-- Description -->
                <?php if (!empty($product['productDesc'])): ?>
                <div class="detail-row">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                        <div class="detail-row-label">Description</div>
                        <div class="detail-row-val"><?= htmlspecialchars($product['productDesc']) ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Centre name -->
                <div class="detail-row">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <div class="detail-row-label">Centre</div>
                        <div class="detail-row-val"><?= htmlspecialchars($product['centreName'] ?? 'Not set') ?></div>
                    </div>
                </div>

                <!-- Address -->
                <div class="detail-row">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <div>
                        <div class="detail-row-label">Address</div>
                        <div class="detail-row-val"><?= htmlspecialchars($product['centreAddress'] ?? 'Not set') ?></div>
                    </div>
                </div>

                <!-- Map link -->
                <?php if (!empty($product['centreMapLink'])): ?>
                <a href="<?= htmlspecialchars($product['centreMapLink']) ?>" target="_blank" class="detail-map-link">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Open Navigation
                </a>
                <?php endif; ?>
            </div>

            <!-- Collected stamp (only if already collected) -->
            <?php if ($isCollected && !empty($product['collectedAt'])): ?>
            <div class="collected-stamp">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Collected on <?= htmlspecialchars(date('d M Y, h:i A', strtotime($product['collectedAt']))) ?>
            </div>
            <?php endif; ?>

            <!-- Action buttons -->
            <div class="detail-actions">
                <a href="workerCollectionTasks.php" class="btn-back">Back</a>

                <?php if ($isCollected): ?>
                    <button class="btn-collected-done" disabled>&#10003; Collected</button>
                <?php else: ?>
                    <button class="btn-collect" onclick="showConfirm()">Collect</button>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- Confirmation modal -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-box">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <h3>Confirm Collection</h3>
        <p>Once confirmed, this action <strong>cannot be undone</strong>. The item will be marked as collected.</p>
        <div class="modal-actions">
            <button class="modal-cancel" onclick="closeConfirm()">Cancel</button>
            <button class="modal-confirm" onclick="confirmCollect()">Confirm Collect</button>
        </div>
    </div>
</div>

<script>
    function showConfirm() {
        document.getElementById('confirmModal').classList.add('active');
    }
    
    function closeConfirm() {
        document.getElementById('confirmModal').classList.remove('active');
    }

    function confirmCollect() {
        // Disable button to prevent double submit
        document.querySelector('.modal-confirm').disabled = true;
        document.querySelector('.modal-confirm').textContent = 'Processing...';

        var formData = new FormData();
        formData.append('productID', <?= $productID ?>);

        fetch('../PHP_files/markProductCollected.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Swap button to "Collected" without page reload
                closeConfirm();
                var actionsDiv = document.querySelector('.detail-actions');
                var collectBtn = actionsDiv.querySelector('.btn-collect');
                var newBtn = document.createElement('button');
                newBtn.className = 'btn-collected-done';
                newBtn.disabled = true;
                newBtn.textContent = '✓ Collected';
                collectBtn.replaceWith(newBtn);

                // Add collected stamp above buttons
                var stamp = document.createElement('div');
                stamp.className = 'collected-stamp';
                stamp.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:20px;height:20px;color:#6cc551"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Collected just now';
                actionsDiv.insertAdjacentElement('beforebegin', stamp);
            } else {
                closeConfirm();
                alert('Error: ' + data.message);
            }
        })
        .catch(() => {
            closeConfirm();
            alert('Something went wrong. Please try again.');
        });
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>