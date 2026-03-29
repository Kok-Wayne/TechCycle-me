<?php
    include __DIR__ . '/nav.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
        header("Location: login.php");
        exit();
    }

    $workerID = (int) $_SESSION['user_id'];

    // ── PENDING: all products not yet collected, newest first (visible to ALL workers)
    $pendingStmt = $conn->prepare(
        "SELECT p.productID, p.productName, p.productCategory, p.productImage,
                rc.centreName
        FROM products p
        LEFT JOIN recycling_centre rc ON rc.centreID = p.centreID
        WHERE p.pickupStatus = 'pending'
        ORDER BY p.productID DESC"
    );
    $pendingStmt->execute();
    $allPending = $pendingStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $pendingStmt->close();

    // ── COMPLETED: only items THIS worker collected, newest first (private)
    $completedStmt = $conn->prepare(
        "SELECT p.productID, p.productName, p.productCategory, p.productImage,
                p.collectedAt,
                rc.centreName
        FROM products p
        LEFT JOIN recycling_centre rc ON rc.centreID = p.centreID
        WHERE p.pickupStatus = 'collected'
        AND p.collectedByWorkerID = ?
        ORDER BY p.collectedAt DESC, p.productID DESC"
    );
    $completedStmt->bind_param('i', $workerID);
    $completedStmt->execute();
    $allCompleted = $completedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $completedStmt->close();

    // ── Group completed by time period
    $completedGroups = [
        'Today'      => [],
        'Yesterday'  => [],
        'This Week'  => [],
        'Last Week'  => [],
        'This Month' => [],
        'Last Month' => [],
        'This Year'  => [],
        'Last Year'  => [],
        'Others'     => [],
    ];

    $now       = new DateTime();
    $today     = new DateTime('today');
    $yesterday = new DateTime('yesterday');

    foreach ($allCompleted as $row) {
        $dateStr = $row['collectedAt'] ?? null;
        if (!$dateStr) {
            $completedGroups['Others'][] = $row;
            continue;
        }
        $date = new DateTime($dateStr);

        if      ($date >= $today)
            $completedGroups['Today'][]      = $row;
        elseif  ($date >= $yesterday)
            $completedGroups['Yesterday'][]  = $row;
        elseif  ($date >= new DateTime('monday this week'))
            $completedGroups['This Week'][]  = $row;
        elseif  ($date >= new DateTime('monday last week'))
            $completedGroups['Last Week'][]  = $row;
        elseif  ($date->format('Y-m') === $now->format('Y-m'))
            $completedGroups['This Month'][] = $row;
        elseif  ($date->format('Y-m') === (new DateTime('first day of last month'))->format('Y-m'))
            $completedGroups['Last Month'][] = $row;
        elseif  ($date->format('Y') === $now->format('Y'))
            $completedGroups['This Year'][]  = $row;
        elseif  ($date->format('Y') === (string)((int)$now->format('Y') - 1))
            $completedGroups['Last Year'][]  = $row;
        else
            $completedGroups['Others'][]     = $row;
    }

    $hasAnyCompleted = count($allCompleted) > 0;
?>

<style>
    body { display: flex; flex-direction: column; min-height: 100vh; }
    #content { flex: 1; }
</style>

<main id="content" class="collection-main">
    <div class="pickups-wrapper">

        <h1 class="pickups-title">Collection Tasks</h1>
        <p class="pickups-subtitle">
            Pending is visible to all workers &nbsp;&middot;&nbsp; Completed shows only your own collections
        </p>

        <!-- Tab bar -->
        <div class="pickup-tab-container">
            <button class="pickup-tab active" onclick="switchTab('pending', this)">
                Pending
            </button>
            <button class="pickup-tab" onclick="switchTab('completed', this)">
                Completed
            </button>
        </div>


        <!-- ═══════════════════════════════
            PENDING PANEL
        ═══════════════════════════════ -->
        <div id="panel-pending" class="pickup-panel active">

            <!-- Search -->
            <div class="pickup-search-wrap">
                <svg class="pickup-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                </svg>
                <input type="text" id="search-pending" class="pickup-search"
                    placeholder="Search product name or location…"
                    oninput="filterPending()">
            </div>

            <div id="list-pending">
                <?php if (empty($allPending)): ?>
                    <div class="pickup-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>No pending pickups right now.</p>
                    </div>
                <?php else: ?>

                    <?php foreach ($allPending as $row): ?>
                    <div class="pickup-item"
                        data-name="<?= strtolower(htmlspecialchars($row['productName'])) ?>"
                        data-location="<?= strtolower(htmlspecialchars($row['centreName'] ?? '')) ?>">

                        <!-- Left: info -->
                        <div class="pickup-item-info">
                            <div class="pickup-item-name">
                                <?= htmlspecialchars($row['productName']) ?>
                            </div>
                            <div class="pickup-item-location">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <?= htmlspecialchars($row['centreName'] ?? 'Location not set') ?>
                            </div>
                            <span class="pickup-cat"><?= htmlspecialchars($row['productCategory']) ?></span>
                        </div>

                        <!-- Right: image -->
                        <?php if (!empty($row['productImage'])): ?>
                            <img class="pickup-item-img"
                                src="/TechCycle/<?= htmlspecialchars($row['productImage']) ?>"
                                alt="<?= htmlspecialchars($row['productName']) ?>">
                        <?php else: ?>
                            <div class="pickup-item-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>

                    </div>
                    <?php endforeach; ?>

                    <div class="pickup-no-results" id="no-results-pending">
                        No results match your search.
                    </div>

                <?php endif; ?>
            </div>
        </div>


        <!-- ═══════════════════════════════
            COMPLETED PANEL
        ═══════════════════════════════ -->
        <div id="panel-completed" class="pickup-panel">

            <!-- Search -->
            <div class="pickup-search-wrap">
                <svg class="pickup-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                </svg>
                <input type="text" id="search-completed" class="pickup-search"
                    placeholder="Search product name or location…"
                    oninput="filterCompleted()">
            </div>

            <div id="list-completed">
                <?php if (!$hasAnyCompleted): ?>
                    <div class="pickup-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>You haven't completed any collections yet.</p>
                    </div>
                <?php else: ?>

                    <?php foreach ($completedGroups as $groupName => $items): ?>
                        <?php if (empty($items)) continue; ?>

                        <div class="pickup-group">
                            <div class="pickup-group-header" onclick="toggleGroup(this)">
                                <span class="pickup-group-arrow">&#8250;</span>
                                <span><?= $groupName ?></span>
                                <span class="pickup-group-count">
                                    <?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?>
                                </span>
                            </div>

                            <div class="pickup-group-body" style="display:none;">
                                <?php foreach ($items as $row): ?>
                                <div class="pickup-item"
                                    data-name="<?= strtolower(htmlspecialchars($row['productName'])) ?>"
                                    data-location="<?= strtolower(htmlspecialchars($row['centreName'] ?? '')) ?>">

                                    <!-- Left: info -->
                                    <div class="pickup-item-info">
                                        <div class="pickup-item-name">
                                            <?= htmlspecialchars($row['productName']) ?>
                                        </div>
                                        <div class="pickup-item-location">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <?= htmlspecialchars($row['centreName'] ?? 'Location not set') ?>
                                        </div>
                                        <?php if (!empty($row['collectedAt'])): ?>
                                        <div class="pickup-item-ts">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Collected <?= htmlspecialchars(date('d M Y, h:i A', strtotime($row['collectedAt']))) ?>
                                        </div>
                                        <?php endif; ?>
                                        <span class="pickup-cat"><?= htmlspecialchars($row['productCategory']) ?></span>
                                    </div>

                                    <!-- Right: image -->
                                    <?php if (!empty($row['productImage'])): ?>
                                        <img class="pickup-item-img"
                                            src="/TechCycle/<?= htmlspecialchars($row['productImage']) ?>"
                                            alt="<?= htmlspecialchars($row['productName']) ?>">
                                    <?php else: ?>
                                        <div class="pickup-item-placeholder">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>

                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php endforeach; ?>

                    <div class="pickup-no-results" id="no-results-completed">
                        No results match your search.
                    </div>

                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<script>
    function switchTab(tab, btn) {
        document.querySelectorAll('.pickup-tab').forEach(function(t) {
            t.classList.remove('active');
        });
        btn.classList.add('active');

        document.querySelectorAll('.pickup-panel').forEach(function(p) {
            p.classList.remove('active');
        });
        document.getElementById('panel-' + tab).classList.add('active');

        // Clear search on tab switch
        var s = document.getElementById('search-' + tab);
        if (s) s.value = '';
        if (tab === 'pending')   filterPending();
        if (tab === 'completed') filterCompleted();
    }

    function toggleGroup(header) {
        header.classList.toggle('open');
        var body = header.nextElementSibling;
        body.style.display = body.style.display === 'none' ? 'block' : 'none';
    }

    function filterPending() {
        var q = document.getElementById('search-pending').value.toLowerCase().trim();
        var items = document.querySelectorAll('#list-pending .pickup-item');
        var visible = 0;

        items.forEach(function(item) {
            var match = !q || item.dataset.name.includes(q) || item.dataset.location.includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        var nr = document.getElementById('no-results-pending');
        if (nr) nr.style.display = (visible === 0 && q) ? 'block' : 'none';
    }

    function filterCompleted() {
        var q = document.getElementById('search-completed').value.toLowerCase().trim();
        var groups = document.querySelectorAll('#list-completed .pickup-group');
        var totalVisible = 0;

        groups.forEach(function(group) {
            var body   = group.querySelector('.pickup-group-body');
            var header = group.querySelector('.pickup-group-header');
            var items  = group.querySelectorAll('.pickup-item');
            var gv = 0;

            items.forEach(function(item) {
                var match = !q || item.dataset.name.includes(q) || item.dataset.location.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) gv++;
            });

            if (q && gv > 0) {
                body.style.display = 'block';
                header.classList.add('open');
            } else if (!q) {
                body.style.display = 'none';
                header.classList.remove('open');
            }

            group.style.display = (gv === 0 && q) ? 'none' : '';
            totalVisible += gv;
        });

        var nr = document.getElementById('no-results-completed');
        if (nr) nr.style.display = (totalVisible === 0 && q) ? 'block' : 'none';
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>