<?php
require '../db_connection.php';

// Check if kidneyTransplantAdvertisementId is set
if (isset($_GET['kidneyTransplantAdvertisementId'])) {
    $kidneyTransplantAdvertisementId = $_GET['kidneyTransplantAdvertisementId'];

    // Prepare and execute delete query
    $sql = "DELETE FROM kidneytransplantadvertisement WHERE kidneyTransplantAdvertisementId=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $kidneyTransplantAdvertisementId);

    if ($stmt->execute()) {
        // Display success alert and redirect
        echo "<script>
                alert('Advertisement deleted successfully');
                window.location.href='../../php/Hospitals/HospitalDashBoard.php';
              </script>";
    } else {
        // Error handling
        echo "Error deleting record: " . $db->error;
    }

    $stmt->close();
} else {
    echo "No advertisement ID provided.";
}

$db->close();
?>
