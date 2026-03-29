<?php
include __DIR__ . '/nav.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

// No product JOIN needed — title is stored directly in notifications now
$stmt = $conn->prepare(
    "SELECT notificationID, title, message, createdAt, isRead
     FROM notifications
     WHERE receiverID = ?
     ORDER BY createdAt DESC"
);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// ── Group by time period ──────────────────────────────────────
$allGroups = [
    'Today'      => [],
    'Yesterday'  => [],
    'This Week'  => [],
    'Last Week'  => [],
    'This Month' => [],
    'Older'      => [],
];

$now       = new DateTime();
$today     = new DateTime('today');
$yesterday = new DateTime('yesterday');

while ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['createdAt']);

    if      ($date >= $today)                                   $allGroups['Today'][]      = $row;
    elseif  ($date >= $yesterday)                               $allGroups['Yesterday'][]  = $row;
    elseif  ($date >= new DateTime('monday this week'))         $allGroups['This Week'][]  = $row;
    elseif  ($date >= new DateTime('monday last week'))         $allGroups['Last Week'][]  = $row;
    elseif  ($date->format('Y-m') === $now->format('Y-m'))     $allGroups['This Month'][] = $row;
    else                                                        $allGroups['Older'][]      = $row;
}

// Total unread count
$unreadCount = 0;
foreach ($allGroups as $items) {
    foreach ($items as $item) {
        if ($item['isRead'] == 0) $unreadCount++;
    }
}

// Build unread-only groups
$unreadGroups = [];
foreach ($allGroups as $groupName => $items) {
    $unread = array_filter($items, fn($r) => $r['isRead'] == 0);
    if (!empty($unread)) $unreadGroups[$groupName] = array_values($unread);
}

// ── Render helper ─────────────────────────────────────────────
function renderGroups(array $groups): void {
    $total = array_sum(array_map('count', $groups));
    if ($total === 0) {
        echo '<p class="muted notif-empty">No notifications here.</p>';
        return;
    }
    foreach ($groups as $groupName => $items) {
        if (empty($items)) continue; ?>
        <div class="notif-group">
            <div class="notif-group-header open" onclick="toggleGroup(this)">
                <span class="notif-arrow">&#8250;</span>
                <span><?= htmlspecialchars($groupName) ?></span>
                <span class="notif-group-count"><?= count($items) ?></span>
            </div>
            <div class="notif-group-body">
                <?php foreach ($items as $row):
                    $isUnread = $row['isRead'] == 0;
                    $ts       = date('d M Y, h:i A', strtotime($row['createdAt']));
                ?>
                <a class="notif-card <?= $isUnread ? 'notif-card--unread' : '' ?>"
                   href="viewNotificationDetail.php?id=<?= urlencode($row['notificationID']) ?>">

                    <span class="notif-dot <?= $isUnread ? 'notif-dot--active' : '' ?>"></span>

                    <div class="notif-card-body">
                        <p class="notif-card-title <?= $isUnread ? 'notif-card-title--bold' : '' ?>">
                            <?= htmlspecialchars($row['title']) ?>
                        </p>
                        <p class="notif-card-ts"><?= $ts ?></p>
                    </div>

                    <svg class="notif-card-chevron" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php }
}
?>

<main id="content">
<div class="notif-wrapper">

    <h1 class="notif-page-title">Notifications</h1>

    <div class="notif-tab-container">
        <button class="notif-tab active" onclick="switchTab('all', this)">All</button>
        <button class="notif-tab" onclick="switchTab('unread', this)">
            Unread<?php if ($unreadCount > 0): ?>
                <span class="notif-tab-badge"><?= $unreadCount ?></span>
            <?php endif; ?>
        </button>
    </div>

    <div id="tab-all">
        <?php renderGroups($allGroups); ?>
    </div>

    <div id="tab-unread" style="display:none;">
        <?php renderGroups($unreadGroups); ?>
    </div>

</div>
</main>

<style>
.notif-page-title {
    font-size: clamp(1.4rem, 3vw, 2rem);
    font-weight: 800;
    color: var(--green-dark);
    margin: 0 0 1rem;
}
.notif-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--green-leaf);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    border-radius: 20px;
    padding: 0.05rem 0.45rem;
    margin-left: 0.35rem;
    vertical-align: middle;
}
.notif-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
    text-decoration: none;
    color: inherit;
    transition: background 0.15s;
}
.notif-card:last-child  { border-bottom: none; }
.notif-card:hover       { background: #f3f7f0; }
.notif-card--unread     { background: #f7fbf3; }
.notif-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: transparent;
    flex-shrink: 0;
}
.notif-dot--active      { background: var(--green-leaf); }
.notif-card-body        { flex: 1; min-width: 0; }
.notif-card-title {
    margin: 0 0 0.15rem;
    font-size: 0.95rem;
    color: var(--text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notif-card-title--bold { font-weight: 700; }
.notif-card-ts {
    margin: 0;
    font-size: 0.78rem;
    color: var(--text-muted);
}
.notif-card-chevron {
    width: 16px;
    height: 16px;
    color: #d1d5db;
    flex-shrink: 0;
}
.notif-group-body       { display: block; }
</style>

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