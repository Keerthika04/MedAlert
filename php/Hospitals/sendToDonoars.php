<?php
session_start();
require '../db_connection.php';
require "../Mail/phpmailer/PHPMailerAutoload.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventid = $_POST['eventid'];

    // Fetch event details
    $stmt = $db->prepare("SELECT e.name, c.name as Cname, e.banner, e.description, e.date, e.time, e.location FROM events e JOIN campaigners c ON e.campaignersId = c.campaignersId WHERE eventid = ?");
    $stmt->bind_param('s', $eventid);
    $stmt->execute();
    $eventResult = $stmt->get_result();

    if ($eventResult->num_rows > 0) {
        $event = $eventResult->fetch_assoc();
        $eventName = $event['name'];
        $eventBanner = $event['banner'];
        $eventCampaigner = $event['Cname'];
        $eventDescription = $event['description'];
        $eventDate = $event['date'];
        $eventTime = $event['time'];
        $eventLocation = $event['location'];

        // Fetch donor emails
        $donorResult = $db->query("SELECT email FROM donors");

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Username = 'jeyandrankeerthika5@gmail.com';
        $mail->Password = 'mojfidopvduiutfs';

        $mail->setFrom('jeyandrankeerthika5@gmail.com', 'MedAlert');

        while ($donor = $donorResult->fetch_assoc()) {

            $donorEmail = $donor['email'];

            $mail->addAddress($donorEmail);
            $mail->isHTML(true);
            $mail->Subject = "Upcoming Event: $eventName";
            $mail->Body = "<p>Dear Donor,</p>
            <p>We are excited to announce an upcoming event organized by $eventCampaigner!</p>
            <p><b>Event Name:</b> $eventName<br>
            <b>Date:</b> $eventDate<br>
            <b>Time:</b> $eventTime<br>
            <b>Location:</b> $eventLocation</p>
            <b>Description:</b> $eventDescription<br>
            <br><br><p>Best regards,<br><b>MedAlert Team</b></p>";
            $mail->addAttachment("../../camp_post/" . $eventBanner);

            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }

            // Clear all addresses for the next loop
            $mail->clearAddresses();
        }
        $updateStmt = $db->prepare("UPDATE events SET shared = '1' WHERE eventid = ?");
        $updateStmt->bind_param('i', $eventid);
        if ($updateStmt->execute()) {
        header("Location: HospitalDashBoard.php");
        exit;
        }
        $updateStmt->close();
    } else {
        header("Location: HospitalDashBoard.php");
        exit;
    }

    $stmt->close();
    $db->close();
}
