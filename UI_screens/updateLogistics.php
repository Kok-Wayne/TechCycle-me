<?php
include __DIR__ . '/nav.php';

$id = $_GET['id'];

// Fetch existing data
$result = $conn->query("SELECT * FROM logistics WHERE id=$id");
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $conn->query("UPDATE logistics 
                  SET name='$name', description='$description', status='$status' 
                  WHERE id=$id");

    header("Location: adminLogistics.php");
    exit();
}
?>

<div style="
    max-width: 600px;
    margin: 40px auto;
    padding: 25px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
">

    <h2 style="
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    ">
        Update Logistics
    </h2>

    <form method="POST">

        <label style="font-weight: bold; display:block; margin-top: 10px;">Name:</label>
        <input type="text" name="name" value="<?= $data['name'] ?>" required
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">

        <label style="font-weight: bold; display:block; margin-top: 15px;">Description:</label>
        <textarea name="description"
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px; height:100px;"><?= $data['description'] ?></textarea>

        <label style="font-weight: bold; display:block; margin-top: 15px;">Status:</label>
        <select name="status"
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">
            <option value="available" <?= $data['status']=='available'?'selected':'' ?>>Available</option>
            <option value="assigned" <?= $data['status']=='assigned'?'selected':'' ?>>Assigned</option>
            <option value="completed" <?= $data['status']=='completed'?'selected':'' ?>>Completed</option>
        </select>

        <button type="submit"
            style="
                width:100%;
                margin-top:20px;
                padding:12px;
                background:#447604;
                color:white;
                border:none;
                border-radius:8px;
                font-size:16px;
                cursor:pointer;
                transition:0.3s;
            "
            onmouseover="this.style.background='#447604'"
            onmouseout="this.style.background='#6cc551'">
            Update Logistics
        </button>

    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>