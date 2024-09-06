<?php
session_start();
require '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $adType = $_POST['adType'];


    if ($adType == 'event') {
        $updateAd = "UPDATE events SET status = '$status' WHERE eventid = '$id'";
    } elseif ($adType == 'kidney') {
        $updateAd = "UPDATE kidneytransplantadvertisement SET status = '$status' WHERE kidneyTransplantAdvertisementId = '$id'";
    }

    if (mysqli_query($db, $updateAd)) {
        header("Location: HospitalDashBoard.php"); 
        exit();
    } else {
        echo "Error updating ad status";
    }
}
?>
