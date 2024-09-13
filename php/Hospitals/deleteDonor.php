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
        // Display success alert and redirect
        echo "<script>
                alert('Donor deleted successfully');
                window.location.href='../../php/Hospitals/HospitalDashBoard.php';
              </script>";
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
