<?php
include __DIR__ . '/nav.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT n.notificationID, n.message, n.createdAt, n.isRead,
            u.username AS senderName
     FROM notifications n
     LEFT JOIN users u ON u.id = n.userID
     WHERE n.triggeredUserID = ? AND n.type = 'worker'
     ORDER BY n.createdAt DESC"
);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$allGroups = [
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

while ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['createdAt']);

    if ($date >= $today) {
        $allGroups['Today'][] = $row;
    } elseif ($date >= $yesterday) {
        $allGroups['Yesterday'][] = $row;
    } elseif ($date >= new DateTime('monday this week')) {
        $allGroups['This Week'][] = $row;
    } elseif ($date >= new DateTime('monday last week')) {
        $allGroups['Last Week'][] = $row;
    } elseif ($date->format('Y-m') === $now->format('Y-m')) {
        $allGroups['This Month'][] = $row;
    } elseif ($date->format('Y-m') === (new DateTime('first day of last month'))->format('Y-m')) {
        $allGroups['Last Month'][] = $row;
    } elseif ($date->format('Y') === $now->format('Y')) {
        $allGroups['This Year'][] = $row;
    } elseif ($date->format('Y') === (string)((int)$now->format('Y') - 1)) {
        $allGroups['Last Year'][] = $row;
    } else {
        $allGroups['Others'][] = $row;
    }
}

$unreadGroups = [];
foreach ($allGroups as $groupName => $items) {
    $unread = array_filter($items, fn($r) => $r['isRead'] == 0);
    if (!empty($unread)) {
        $unreadGroups[$groupName] = array_values($unread);
    }
}

$unreadCount = array_sum(array_map('count', $unreadGroups));

function renderGroups($groups) {
    $total = array_sum(array_map('count', $groups));
    if ($total === 0) {
        echo '<p class="muted notif-empty">No notifications.</p>';
        return;
    }
    foreach ($groups as $groupName => $items) {
        if (empty($items)) continue;
        ?>
        <div class="notif-group">
            <div class="notif-group-header" onclick="toggleGroup(this)">
                <span class="notif-arrow">&#8250;</span>
                <span><?= $groupName ?></span>
            </div>
            <div class="notif-group-body" style="display:none;">
                <?php foreach ($items as $row): ?>
                    <div class="notif-item">
                        <p class="notif-message <?= $row['isRead'] == 0 ? 'notif-unread' : '' ?>">
                            <?= htmlspecialchars($row['message']) ?>
                        </p>
                        <p class="notif-timestamp">
                            <?php if (!empty($row['senderName'])): ?>
                                From: <?= htmlspecialchars($row['senderName']) ?> &nbsp;·&nbsp;
                            <?php endif; ?>
                            <?= htmlspecialchars($row['createdAt']) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
?>

<main id="content" style="display:flex; flex-direction:column; min-height:calc(100vh - 60px);">

    <div class="notif-wrapper" style="flex:1;">

        <h1>My Notifications</h1>

        <!-- Tabs -->
        <div class="notif-tab-container">
            <button class="notif-tab active" onclick="switchTab('all', this)">All</button>
            <button class="notif-tab" onclick="switchTab('unread', this)">
                Unread<?php if ($unreadCount > 0): ?> (<?= $unreadCount ?>)<?php endif; ?>
            </button>
        </div>

        <!-- All tab -->
        <div id="tab-all">
            <?php renderGroups($allGroups); ?>
        </div>

        <!-- Unread tab -->
        <div id="tab-unread" style="display:none;">
            <?php renderGroups($unreadGroups); ?>
        </div>

    </div>

</main>

<script>
function toggleGroup(header) {
    header.classList.toggle('open');
    const body = header.nextElementSibling;
    body.style.display = body.style.display === 'none' ? 'block' : 'none';
}

function switchTab(tab, btn) {
    document.querySelectorAll('.notif-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-all').style.display    = tab === 'all'    ? 'block' : 'none';
    document.getElementById('tab-unread').style.display = tab === 'unread' ? 'block' : 'none';
}
</script>

<?php include __DIR__ . '/footer.php'; ?>