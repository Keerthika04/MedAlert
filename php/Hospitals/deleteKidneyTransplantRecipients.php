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
        // Successful deletion, redirect or display a success message
        header("Location: ../../php/Hospitals/HospitalDashBoard.php?message=Advertisement+deleted+successfully");
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
