<?php
include __DIR__ . '/nav.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Image upload
    $image = $_FILES['image']['name'];
    $target = "../images/" . basename($image);

    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // Save to DB
    $stmt = $conn->prepare("INSERT INTO logistics (name, description, image_path, status) VALUES (?, ?, ?, ?)");
    $imagePath = "images/" . $image;

    $stmt->bind_param("ssss", $name, $description, $imagePath, $status);
    $stmt->execute();

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
        Create Logistics Item
    </h2>

    <form method="POST" enctype="multipart/form-data">

        <label style="font-weight: bold; display:block; margin-top: 10px;">Name:</label>
        <input type="text" name="name" required
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">

        <label style="font-weight: bold; display:block; margin-top: 15px;">Description:</label>
        <textarea name="description" required
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px; height:100px;"></textarea>

        <label style="font-weight: bold; display:block; margin-top: 15px;">Status:</label>
        <select name="status"
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;">
            <option value="available">Available</option>
            <option value="assigned">Assigned</option>
            <option value="completed">Completed</option>
        </select>

        <label style="font-weight: bold; display:block; margin-top: 15px;">Upload Image:</label>
        <input type="file" name="image" required
            style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px; background:#f9f9f9;">

        <button type="submit"
            style="
                width:100%;
                margin-top:20px;
                padding:12px;
                background:#28a745;
                color:white;
                border:none;
                border-radius:8px;
                font-size:16px;
                cursor:pointer;
                transition:0.3s;
            "
            onmouseover="this.style.background='#6cc551'"
            onmouseout="this.style.background='#447604'">
            Add Logistics
        </button>

    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>