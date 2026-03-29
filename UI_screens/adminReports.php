<?php
include __DIR__ . '/nav.php';

// Restrict access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

// 1. User roles
$userRoles = [];
$res = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
while ($row = $res->fetch_assoc()) {
    $userRoles[$row['role']] = $row['count'];
}

// 2. Product categories
$productCategories = [];
$res = $conn->query("SELECT category, COUNT(*) as count FROM products GROUP BY category");
while ($row = $res->fetch_assoc()) {
    $productCategories[$row['category']] = $row['count'];
}

// 3. Order status
$orderStatus = [];
$res = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
while ($row = $res->fetch_assoc()) {
    $orderStatus[$row['status']] = $row['count'];
}

// 4. Orders over time
$orderDates = [];
$res = $conn->query("
    SELECT DATE(orderPlaced) as date, COUNT(*) as count 
    FROM orders 
    GROUP BY DATE(orderPlaced)
    ORDER BY date ASC
");
while ($row = $res->fetch_assoc()) {
    $orderDates[$row['date']] = $row['count'];
}

// 5. Notifications read vs unread
$notifStats = ['Read' => 0, 'Unread' => 0];
$res = $conn->query("
    SELECT isRead, COUNT(*) as count 
    FROM notifications 
    GROUP BY isRead
");
while ($row = $res->fetch_assoc()) {
    if ($row['isRead'] == 1) $notifStats['Read'] = $row['count'];
    else $notifStats['Unread'] = $row['count'];
}

// Fetch stats
$totalUserCount = $conn->query("SELECT COUNT(*) as total 
FROM users 
WHERE role IN ('user', 'worker')")->fetch_assoc()['total'];
$workerCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='worker'")->fetch_assoc()['total'];
$userCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$productCount = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
?>

<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <header>
            <h1>Admin Reports Dashboard</h1>
        </header>

        <nav></nav> <!-- This is required for grid layout -->

        <main>
            <div class="grid">
                <!-- USER ROLES -->
                <div class="card">
                    <h3>User Distribution</h3>
                    <canvas id="userChart"></canvas>
                </div>

                <!-- PRODUCT CATEGORY -->
                <div class="card">
                    <h3>Product Categories</h3>
                    <canvas id="productChart"></canvas>
                </div>

                <!-- ORDER STATUS -->
                <div class="card">
                    <h3>Order Status</h3>
                    <canvas id="orderChart"></canvas>
                </div>

                <!-- NOTIFICATIONS -->
                <div class="card">
                    <h3>Notifications</h3>
                    <canvas id="notifChart"></canvas>
                </div>
            </div>

            <!-- ORDERS OVER TIME -->
            <div class="card" style="margin-top:20px;">
                <h3>Orders Over Time</h3>
                <canvas id="timeChart"></canvas>
            </div>
        </main>

        <aside id="quick-stats">
            <h2>Quick Stats</h2>
            <div class="card">
                <h3>Total Users</h3>
                <p><?= $totalUserCount ?></p>
            </div>

            <div class="card">
                <h3>Regular Users</h3>
                <p><?= $userCount ?></p>
            </div>

            <div class="card">
                <h3>Workers</h3>
                <p><?= $workerCount ?></p>
            </div>

            <div class="card">
                <h3>Products</h3>
                <p><?= $productCount ?></p>
            </div>
        </aside>
        <script>
            // USER ROLES
            new Chart(document.getElementById('userChart'), {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_keys($userRoles)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($userRoles)) ?>
                    }]
                }
            });

            // PRODUCT CATEGORY
            new Chart(document.getElementById('productChart'), {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_keys($productCategories)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($productCategories)) ?>
                    }]
                }
            });

            // ORDER STATUS
            new Chart(document.getElementById('orderChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_keys($orderStatus)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($orderStatus)) ?>
                    }]
                }
            });

            // NOTIFICATIONS
            new Chart(document.getElementById('notifChart'), {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_keys($notifStats)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($notifStats)) ?>
                    }]
                }
            });

            // ORDERS OVER TIME
            new Chart(document.getElementById('timeChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_keys($orderDates)) ?>,
                    datasets: [{
                        label: "Orders",
                        data: <?= json_encode(array_values($orderDates)) ?>
                    }]
                }
            });
        </script>
    </div>
</body>
<?php include 'footer.php' ?>