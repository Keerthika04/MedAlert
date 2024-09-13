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
        // Display success alert and redirect
        echo "<script>
                alert('Hospital deleted successfully');
                window.location.href='../../php/Hospitals/HospitalDashBoard.php';
              </script>";
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
