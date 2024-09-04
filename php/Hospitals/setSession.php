<?php
session_start();

if (isset($_GET['section'])) {
    $_SESSION['activeSection'] = $_GET['section'];
    
    header('Location: HospitalDashBoard.php');
    exit;
} else {
    header('Location: HospitalDashBoard.php');
    exit;
}
?>
