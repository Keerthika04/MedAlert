<?php
session_start();
require '../db_connection.php';

if (isset($_POST['qrData'])) {
    $eventId = $_GET['qrData'];
    $donorId = $_SESSION['donorId'];

    $sql = "SELECT Historyid FROM blooddonationhistory WHERE donorId = ? AND eventid = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $donorId, $eventId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Record already exists.";
    } else {
        $query = $db->query("SELECT Historyid FROM blooddonationhistory ORDER BY Historyid DESC LIMIT 1");
        $new_history_id = "H00000000001"; 

        if ($query->num_rows > 0) {
            $row = $query->fetch_assoc();
            $last_id = $row['Historyid'];
            $num = (int) substr($last_id, 1) + 1;
            $new_history_id = "H" . str_pad($num, 11, "0", STR_PAD_LEFT);
        }
        
        $insertSql = "INSERT INTO blooddonationhistory (Historyid, eventid, donorId) VALUES (?, ?, ?)";
        $insertStmt = $db->prepare($insertSql);
        $insertStmt->bind_param("sss",$new_history_id, $eventId, $donorId);

        if ($insertStmt->execute()) {
            $_SESSION['success_msg'] = "Successfully Marked";
        } else {
            $_SESSION['error_msg'] = "Try Again!";
        }
    }

    $stmt->close();
    $insertStmt->close();
    $db->close();
} else {
    echo "No data received.";
}
?>
