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
    
    $duplicate_check_stmt = $db->prepare("
        SELECT campaignersId FROM campaigners 
        WHERE (email = ? OR username = ?) AND campaignersId != ?
    ");
    $duplicate_check_stmt->bind_param("sss", $email, $username, $campaignersId);
    $duplicate_check_stmt->execute();
    $duplicate_check_stmt->store_result();

    if ($duplicate_check_stmt->num_rows > 0) {
        echo "<script>alert(' Email or Username already exists.'); window.location.href='CampaignerDashBoard.php';</script>";
    } else {
        $stmt = $db->prepare("
            UPDATE campaigners 
            SET name = ?, address = ?, contact = ?, email = ?, description = ?, username = ? 
            WHERE campaignersId = ?
        ");
        $stmt->bind_param("sssssss", $name, $address, $contact, $email, $description, $username, $campaignersId);

        if ($stmt->execute()) {
            header("Location: CampaignerDashBoard.php");
            exit();
        } else {
            echo "<script>alert('Error updating record.'); window.location.href='CampaignerDashBoard.php';</script>";
        }

        $stmt->close();
    }

    $duplicate_check_stmt->close();
}

$db->close();
?>
