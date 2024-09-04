<?php
session_start();
require '../db_connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospitalId = $_SESSION['hospitalId'];
    $hospitalName = htmlspecialchars($_POST['hospitalName']);
    $address = htmlspecialchars($_POST['address']);
    $contact = htmlspecialchars($_POST['contact']);
    $email = htmlspecialchars($_POST['email']);
    
    // Prepare SQL update statement
    $stmt = $db->prepare("UPDATE hospitals SET hospitalName = ?, address = ?, contact = ?, email = ? WHERE hospitalId = ?");
    $stmt->bind_param("sssss", $hospitalName, $address, $contact, $email, $hospitalId);

    if ($stmt->execute()) {
        header("Location: HospitalDashBoard.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}

$db->close();
?>