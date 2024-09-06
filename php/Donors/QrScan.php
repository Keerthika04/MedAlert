<?php
session_start();
require '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qrData'])) {
    $donorId = $_SESSION['donorId'];
    $eventId = $_POST['qrData'];

    $sql = "SELECT Historyid FROM blooddonationhistory WHERE donorId = ? AND eventid = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param("ss", $donorId, $eventId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Record already exists.'); window.location.href = 'DonorsDashboard.php';</script>";
    } else {
        $query = $db->query("SELECT Historyid FROM blooddonationhistory ORDER BY Historyid DESC LIMIT 1");
        if (!$query) {
            die("Query failed: " . $db->error);
        }
        
        $new_history_id = "H00000000001"; 

        if ($query->num_rows > 0) {
            $row = $query->fetch_assoc();
            $last_id = $row['Historyid'];
            $num = (int) substr($last_id, 1) + 1;
            $new_history_id = "H" . str_pad($num, 11, "0", STR_PAD_LEFT);
        }
        
        $insertSql = "INSERT INTO blooddonationhistory (Historyid, eventid, donorId) VALUES (?, ?, ?)";
        $insertStmt = $db->prepare($insertSql);
        if (!$insertStmt) {
            die("Prepare failed: " . $db->error);
        }
        $insertStmt->bind_param("sss", $new_history_id, $eventId, $donorId);

        if ($insertStmt->execute()) {
            echo "<script>alert('Attendance marked successfully!'); window.location.href = 'DonorsDashboard.php';</script>";
        } else {
            echo "<script>alert('Failed to mark attendance. Please try again.  $eventId'); window.location.href = 'DonorsDashboard.php';</script>";
            error_log("Insert failed: " . $insertStmt->error);
        }

        $insertStmt->close();
    }

    $stmt->close();
    $db->close();
} else {
    echo "No data received.";
}
?>
