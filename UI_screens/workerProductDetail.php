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

<style>
    body { display: flex; flex-direction: column; min-height: 100vh; }
    #content { flex: 1; }

    .detail-wrapper {
        max-width: 640px;
        margin: 2rem auto;
        padding: 0 1.25rem 3rem;
    }

    /* Back link */
    .detail-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        margin-bottom: 1.25rem;
    }
    .detail-back:hover { color: var(--green-dark); }

    /* Product image */
    .detail-img-wrap {
        width: 100%;
        background: #f3f4f6;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 220px;
    }
    .detail-img-wrap img {
        width: 100%;
        max-height: 340px;
        object-fit: contain;
        display: block;
    }
    .detail-img-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 220px;
        color: #d1d5db;
    }
    .detail-img-placeholder svg { width: 64px; height: 64px; }

    /* Info card */
    .detail-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .detail-product-name {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--green-dark);
        margin: 0 0 0.25rem;
    }
    .detail-category {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--teal);
        background: #e8f7f4;
        border-radius: 20px;
        padding: 0.1rem 0.6rem;
        margin-bottom: 1rem;
        text-transform: capitalize;
    }
    .detail-row {
        display: flex;
        gap: 0.6rem;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        font-size: 0.93rem;
        color: var(--text-dark);
    }
    .detail-row svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        margin-top: 2px;
        color: var(--teal);
    }
    .detail-row-label {
        font-weight: 700;
        min-width: 80px;
        color: var(--text-muted);
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-top: 1px;
    }
    .detail-row-val { flex: 1; line-height: 1.5; }

    /* Map link */
    .detail-map-link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--green-leaf);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        margin-top: 0.5rem;
        padding: 0.5rem 1rem;
        border: 1.5px solid var(--green-leaf);
        border-radius: 10px;
        transition: background 0.18s, color 0.18s;
    }
    .detail-map-link:hover { background: var(--green-leaf); color: #fff; }
    .detail-map-link svg { width: 16px; height: 16px; }

    /* Collected stamp */
    .collected-stamp {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #e8f5e0;
        border: 1.5px solid #6cc551;
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-weight: 700;
        color: var(--green-dark);
        font-size: 0.9rem;
        margin-bottom: 1.25rem;
    }
    .collected-stamp svg { width: 20px; height: 20px; color: var(--green-leaf); }

    /* Action buttons */
    .detail-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .btn-back {
        flex: 1;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        border: 1.5px solid #d1d5db;
        background: #f3f4f6;
        color: var(--text-dark);
        text-align: center;
        text-decoration: none;
        transition: background 0.18s;
    }
    .btn-back:hover { background: #e5e7eb; }

    .btn-collect {
        flex: 1;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        border: none;
        background: var(--green-leaf);
        color: #fff;
        transition: background 0.18s;
    }
    .btn-collect:hover { background: var(--green-dark); }

    .btn-collected-done {
        flex: 1;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        border: 1.5px solid #6cc551;
        background: #e8f5e0;
        color: var(--green-dark);
        cursor: default;
    }

    /* Warning modal overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: #fff;
        border-radius: 18px;
        padding: 2rem 1.75rem;
        max-width: 380px;
        width: 90%;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        text-align: center;
    }
    .modal-box svg { width: 48px; height: 48px; color: #f59e0b; margin: 0 auto 1rem; display: block; }
    .modal-box h3 { margin: 0 0 0.5rem; color: var(--text-dark); font-size: 1.1rem; }
    .modal-box p  { color: var(--text-muted); font-size: 0.9rem; margin: 0 0 1.5rem; }
    .modal-actions { display: flex; gap: 0.75rem; }
    .modal-cancel {
        flex: 1; padding: 0.7rem; border-radius: 10px;
        border: 1.5px solid #d1d5db; background: #f3f4f6;
        font-weight: 700; cursor: pointer; font-size: 0.9rem;
    }
    .modal-confirm {
        flex: 1; padding: 0.7rem; border-radius: 10px;
        border: none; background: var(--green-leaf);
        color: #fff; font-weight: 700; cursor: pointer; font-size: 0.9rem;
    }
    .modal-confirm:hover { background: var(--green-dark); }
</style>

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

    fetch('markProductCollected.php', {
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