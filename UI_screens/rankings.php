<?php
include __DIR__ . '/nav.php';

// Fetch full leaderboard ordered by points DESC
$leaderboard = $conn->query("
    SELECT u.id AS userID, u.username, up.points
    FROM user_points up
    JOIN users u ON up.userID = u.id
    ORDER BY up.points DESC
");

// Get current user ID
$currentUserID = $_SESSION['user_id'];

$rank = 1;
$userRank = null;
$userPoints = 0;

// Loop once to find current user's rank
while ($row = $leaderboard->fetch_assoc()) {
    if ($row['userID'] == $currentUserID) {
        $userRank = $rank;
        $userPoints = $row['points'];
        break;
    }
    $rank++;
}

// Reset pointer for full leaderboard display
$leaderboard->data_seek(0);
?>

<main id="content">

    <!-- HERO -->
    <section class="hero">
        <h1>Community Leaderboard</h1>
        <p>Top contributors helping recycle e-waste</p>
    </section>

    <!-- CURRENT USER RANK -->
    <section class="card" style="text-align:center; background:#f0f9f0;">
        <h2>Your Rank</h2>
        <?php if ($userRank): ?>
            <p style="font-size:20px; font-weight:bold; color:#28a745;">
                Rank #<?= $userRank ?> — <?= htmlspecialchars($_SESSION['username']) ?>
            </p>
            <p style="font-size:18px;">Points: <?= $userPoints ?> pts</p>
        <?php else: ?>
            <p>You have no points yet. Start donating e-waste to earn points!</p>
        <?php endif; ?>
    </section>

    <!-- FULL LEADERBOARD -->
    <section class="card">
        <h2>Top Users</h2>
        <div class="grid">
            <?php
            $rank = 1;
            while ($row = $leaderboard->fetch_assoc()): 
                $highlight = ($row['userID'] == $currentUserID) ? "background:#e6f7ff;" : "";
            ?>
                <div class="card" style="text-align:center; <?= $highlight ?>">
                    <h3>
                        <?php if ($rank == 1): ?> No.1
                        <?php elseif ($rank == 2): ?> No.2
                        <?php elseif ($rank == 3): ?> No.3
                        <?php endif; ?>
                        <?= htmlspecialchars($row['username']) ?>
                    </h3>

                    <p class="muted">Rank #<?= $rank ?></p>

                    <p style="font-size:20px; font-weight:bold; color:#28a745; margin-top:10px;">
                        <?= $row['points'] ?> pts
                    </p>
                </div>
            <?php 
                $rank++;
            endwhile; 
            ?>
        </div>
    </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>