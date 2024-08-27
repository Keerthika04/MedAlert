<?php
require '../db_connection.php';
require "../Mail/phpmailer/PHPMailerAutoload.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['eventid'];
    $campaignerEmail = $_POST['campaignerEmail'];
    $eventName = $_POST['eventName'];
    $imageData = $_POST['image'];

    // Extract image data
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = base64_decode($imageData);

    // Generate a unique filename for the QR code
    $filename = 'qrcode_' . $eventId . '.png';
    $filePath = '../../QR/' . $filename;

    // Save the QR code image to the file system
    file_put_contents($filePath, $imageData);

    // Generate a new donor ID
    $query = $db->query("SELECT qrId FROM qrattendance ORDER BY qrId DESC LIMIT 1");
    $new_event_id = "Q00000000001"; // Default starting ID

    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $last_id = $row['qrId'];
        $num = (int) substr($last_id, 1) + 1;
        $new_event_id = "Q" . str_pad($num, 11, "0", STR_PAD_LEFT);
    }

    // Save the QR code details to the database
    $stmt = $db->prepare("INSERT INTO qrattendance (qrId,qrImage, eventId) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $new_event_id, $filename, $eventId);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        if ($campaignerEmail != null) {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';

            $mail->Username = 'jeyandrankeerthika5@gmail.com';
            $mail->Password = 'mojfidopvduiutfs';

            $mail->setFrom('jeyandrankeerthika5@gmail.com', 'Request');
            $mail->addAddress($campaignerEmail);

            $mail->isHTML(true);
            $mail->Subject = "Your Request has been accepted!";
            $mail->Body = "<p>Dear Campaigner,<br><br>
                            Your event request for '$eventName' has been accepted. Please find the attached QR code for the event.<br>
                            This QR code will be used for marking donor's attendance during the event. Please ensure that it is displayed prominently at the event location.<br><br>
                            Thank you for your efforts in organizing this event.<br><br>
                            <b>From MedAlert</b></p>";

            // Attach the QR code image
            $mail->addAttachment($filePath);

            if (!$mail->send()) {
                echo "Register Failed, Invalid Email ";
            } else {
                echo json_encode(['success' => true]);
            }
        }
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $db->close();
}
