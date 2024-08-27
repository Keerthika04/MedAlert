<?php
session_start();
require '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventid = $_POST['eventid'];
    $action = $_POST['action'];
    $campaignerEmail = $_POST['campaignerEmail'];
    $eventName = $_POST['eventName'];

    // Determine status based on action
    $status = ($action == '1') ? '1' : '2';

    // Update event status
    $stmt = $db->prepare("UPDATE events SET status = ? WHERE eventid = ?");
    $stmt->bind_param('is', $status, $eventid);

    require "../Mail/phpmailer/PHPMailerAutoload.php";
    $mail = new PHPMailer(true);

    if ($stmt->execute()) {
        if ($status == '2') {
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
            $mail->Subject = "Your Request has been accepted";
            $mail->Body = "<p>Dear Campaigner, <br> We regret to inform you that your event request for '$eventName' has been reviewed and unfortunately rejected. <br> For further inquiries, please contact us. <br><br>
                <b>From MedAlert</b>";

            if (!$mail->send()) {
                echo "Register Failed, Invalid Email ";
            }
            header("Location: manageCampaignersRequest.php");
            exit;
        }else{
            header("Location: manageCampaignersRequest.php");
            exit;
        }
    } else {
        echo "Error updating event: " . $db->error;
    }

    $stmt->close();
}
