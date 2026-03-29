<?php
include __DIR__ . '/dbConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['centreID'];

    $stmt = $conn->prepare("DELETE FROM recycling_centre WHERE centreID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../UI_screens/recyclingCentre.php?success=deleted");
        exit();
    } else {
        header("Location: ../UI_screens/deleteRecyclingCentre.php?id=$id&error=failed");
        exit();
    }
}