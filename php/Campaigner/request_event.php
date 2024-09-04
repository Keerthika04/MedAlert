<?php
session_start();
require '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaignersId = $_SESSION['campaignersId'];
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $date = htmlspecialchars($_POST['date']);
    $time = htmlspecialchars($_POST['startTime']) . "-" . htmlspecialchars($_POST['endTime']);
    $location = htmlspecialchars($_POST['location']);
    $hospitalId = htmlspecialchars($_POST['hospitalId']);
    $status = 0;
    $shared = 0;

    $banner = $_FILES['banner']['name'];
    $file_ext = pathinfo($banner, PATHINFO_EXTENSION);
    $unique_name = uniqid() . '.' . $file_ext;
    $target_file = "../../camp_post/" . $unique_name;

    // Generate a new ID
    $query = $db->query("SELECT eventid FROM events ORDER BY eventid DESC LIMIT 1");
    $new_event_id = "E00000000001"; // Default starting ID

    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $last_id = $row['eventid'];
        $num = (int) substr($last_id, 1) + 1;
        $new_event_id = "E" . str_pad($num, 11, "0", STR_PAD_LEFT);
    }


        if (move_uploaded_file($_FILES['banner']['tmp_name'], $target_file)) {
            // Prepare SQL insert statement
            $stmt = $db->prepare("INSERT INTO events (eventid, name, banner, description, campaignersId, date, time, location, hospitalId, status, shared) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssii", $new_event_id, $name, $unique_name, $description, $campaignersId, $date, $time, $location, $hospitalId, $status, $shared);

            if ($stmt->execute()) {
                header("Location: CampaignerDashBoard.php");
                exit();
            } else {
                echo "Error submitting event request: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }

$db->close();
