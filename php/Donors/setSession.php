<?php
session_start();

if (isset($_GET['section'])) {
    $_SESSION['activeSection'] = $_GET['section'];
    
    header('Location: DonorsDashboard.php');
    exit;
} else {
    header('Location: DonorsDashboard.php');
    exit;
}
?>
