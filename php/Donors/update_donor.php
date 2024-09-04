<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donorId = $_SESSION['donorId'];
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $NICnumber = htmlspecialchars($_POST['NICnumber']);
    $weight = htmlspecialchars($_POST['weight']);
    $bloodGroup = htmlspecialchars($_POST['bloodGroup']);
    $donationDuration = htmlspecialchars($_POST['donationDuration']);
    $dateOfBirth = htmlspecialchars($_POST['dateOfBirth']);
    $gender = htmlspecialchars($_POST['gender']);
    $address = htmlspecialchars($_POST['address']);
    $personalContact = htmlspecialchars($_POST['personalContact']);
    $emergencyContact = htmlspecialchars($_POST['emergencyContact']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);

    $stmt = $db->prepare("
        UPDATE donors 
        SET firstName = ?, lastName = ?, NICnumber = ?, weight = ?, bloodGroup = ?, 
            donationDuration = ?, dateOfBirth = ?, gender = ?, address = ?, 
            personalContact = ?, emergencyContact = ?, email = ?, username = ?
        WHERE DonorId = ?
    ");
    
    // Bind parameters
    $stmt->bind_param("sssisissssssss", 
        $firstName, $lastName, $NICnumber, $weight, $bloodGroup, $donationDuration, 
        $dateOfBirth, $gender, $address, $personalContact, $emergencyContact, 
        $email, $username, $donorId
    );
    
    if ($stmt->execute()) {
        header("Location: DonorsDashboard.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
} else {
    header("Location: DonorsDashboard.php");
    exit();
}

$db->close();
?>
