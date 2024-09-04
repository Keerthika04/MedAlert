<?php
require '../db_connection.php';

// Check if hospitalId is set
if (isset($_GET['hospitalId'])) {
    $hospitalId = $_GET['hospitalId'];

    // Prepare and execute delete query
    $sql = "DELETE FROM hospitals WHERE hospitalId=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $hospitalId);

    if ($stmt->execute()) {
        // Successful deletion, redirect or display a success message
        header("Location: ../../php/Hospitals/HospitalDashBoard.php?message=Hospital+deleted+successfully");
    } else {
        // Error handling
        echo "Error deleting record: " . $db->error;
    }

    $stmt->close();
} else {
    echo "No hospital ID provided.";
}

$db->close();
?>
