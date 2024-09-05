<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospitalId = $_SESSION['hospitalId'];
    $hospitalName = htmlspecialchars($_POST['hospitalName']);
    $address = htmlspecialchars($_POST['address']);
    $contact = htmlspecialchars($_POST['contact']);
    $email = htmlspecialchars($_POST['email']);
    
    $stmt = $db->prepare("SELECT hospitalId FROM hospitals WHERE email = ? AND hospitalId != ?");
    $stmt->bind_param("ss", $email, $hospitalId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                alert('The email address is already in use by another hospital.');
                window.location.href = '../../php/Hospitals/HospitalDashBoard.php';
              </script>";
        $stmt->close();
    } else {
        $stmt->close();

        $stmt = $db->prepare("UPDATE hospitals SET hospitalName = ?, address = ?, contact = ?, email = ? WHERE hospitalId = ?");
        $stmt->bind_param("sssss", $hospitalName, $address, $contact, $email, $hospitalId);

        if ($stmt->execute()) {
            header("Location: ../../php/Hospitals/HospitalDashBoard.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    }
}

$db->close();
?>
