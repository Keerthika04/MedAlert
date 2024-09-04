<?php
session_start();

if (isset($_GET['section'])) {
    $_SESSION['activeSection'] = $_GET['section'];
    
    header('Location: CampaignerDashboard.php');
    exit;
} else {
    header('Location: CampaignerDashboard.php');
    exit;
}
?>
