<?php
include __DIR__ . '/nav.php';

// Fetch leaderboard
$result = $conn->query("
    SELECT u.username, up.points
    FROM user_points up
    JOIN users u ON up.userID = u.id
    ORDER BY up.points DESC
");
?>

<main id="content">

    <!-- HERO -->
    <section class="hero">
        <h1>Community Leaderboard</h1>
        <p>Top contributors helping recycle e-waste</p>
    </section>

    <!-- LEADERBOARD -->
    <section class="card">
        <h2>Top Users</h2>

        <div class="grid">
            <?php $rank = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="card" style="text-align:center;">

                    <h3>
                        <?php if ($rank == 1): ?> No.1 
                        <?php elseif ($rank == 2): ?> No.2
                        <?php elseif ($rank == 3): ?> No.3
                        <?php endif; ?>
                        <?= htmlspecialchars($row['username']) ?>
                    </h3>

                    <p class="muted">Rank #<?= $rank ?></p>

                    <p style="
                        font-size: 20px;
                        font-weight: bold;
                        color: #28a745;
                        margin-top: 10px;
                    ">
                        <?= $row['points'] ?> pts
                    </p>

                </div>

                <?php $rank++; ?>
            <?php endwhile; ?>
        </div>

    </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>