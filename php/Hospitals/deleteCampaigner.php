<?php
require '../db_connection.php';

// Check if campaignerId is set
if (isset($_GET['campaignerId'])) {
    $campaignerId = $_GET['campaignerId'];

    // Prepare and execute delete query
    $sql = "DELETE FROM campaigners WHERE campaignersId=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $campaignerId);

    if ($stmt->execute()) {
        // Successful deletion, redirect or display a success message
        header("Location: ../../php/Hospitals/HospitalDashBoard.php?message=Campaigner+deleted+successfully");
    } else {
        // Error handling
        echo "Error deleting record: " . $db->error;
    }

    $stmt->close();
} else {
    echo "No campaigner ID provided.";
}

$db->close();
?>
