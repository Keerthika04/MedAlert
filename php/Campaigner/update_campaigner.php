<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaignersId = $_SESSION['campaignersId'];
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $contact = htmlspecialchars($_POST['contact']);
    $email = htmlspecialchars($_POST['email']);
    $description = htmlspecialchars($_POST['description']);
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    
    // Prepare SQL update statement
    $stmt = $db->prepare("UPDATE campaigners SET name = ?, address = ?, contact = ?, email = ?, description = ?, username = ? WHERE campaignersId = ?");
    $stmt->bind_param("sssssss", $name, $address, $contact, $email, $description, $username, $campaignersId);

    if ($stmt->execute()) {
        header("Location: CampaignerDashBoard.php");
        exit();
    } else {
        header("Location: CampaignerDashBoard.php");
    }

    $stmt->close();
}

$db->close();
?>
