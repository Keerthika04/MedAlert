<?php
require '../db_connection.php';

// Check if donorId is set
if (isset($_GET['donorId'])) {
    $donorId = $_GET['donorId'];

    // Prepare and execute delete query
    $sql = "DELETE FROM donors WHERE Donorid=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $donorId);

    if ($stmt->execute()) {
        // Successful deletion, redirect or display a success message
        header("Location: ../../php/Hospitals/HospitalDashBoard.php?message=Donor+deleted+successfully");
    } else {
        // Error handling
        echo "Error deleting record: " . $db->error;
    }

    $stmt->close();
} else {
    echo "No donor ID provided.";
}

$db->close();
?>
