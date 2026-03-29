<?php
include __DIR__ . '/nav.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch stats
$totalUserCount = $conn->query("SELECT COUNT(*) as total 
FROM users 
WHERE role IN ('user', 'worker')")->fetch_assoc()['total'];
$workerCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='worker'")->fetch_assoc()['total'];
$userCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$productCount = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
?>
<!-- Main -->
<main id="content">
    <!-- Hero -->
    <section class="hero" aria-label="Introduction">
        <div>
            <h1>Admin Dashboard</h1>
            <p>
                Organise pickup sites, manage worker accounts, track website statistics, and list available items.
            </p>
            <div class="actions">
                <a class="button primary" href="#features">Dashboards and Reports</a>
                <a class="button ghost" href="createWorkerAcc.php">Create a worker account</a>
            </div>
        </div>
        <div class="grid hero-cards">

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

        </div>
    </section>

    <!-- Features -->
    <section id="features" class="grid" aria-label="Key features">
        <article class="card">
            <h3>Recycling Centre</h3>
            <p class="muted">Check the recycling centres sites added!</p>
            <a class="button ghost" href="recyclingCentre.php">View address sites</a>
        </article>

        <article class="card">
            <h3>Worker Management</h3>
            <p class="muted">Create and manage worker accounts.</p>
            <a class="button ghost" href="manageWorkers.php">Manage Workers</a>
        </article>

        <article class="card">
            <h3>Point Systems</h3>
            <p class="muted">See who has the most points among each user.</p>
            <a class="button ghost" href="/gardening">Find a garden</a>
        </article>

        <article class="card" id="swap">
            <h3>Logistics</h3>
            <p class="muted">Manage E-waste materials and list them to the public!</p>
            <a class="button ghost" href="adminLogistics.php">Start Listing</a>
        </article>
    </section>

    <?php include __DIR__ . '/footer.php' ?>