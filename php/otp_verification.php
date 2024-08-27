<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="UTF-8">
    <title>MedAlert</title>
    <link rel="icon" href="../Images/logo.png" type="image/png" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" />
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="verification_container">
        <div class="otp">
            <h1>OTP Verification</h1>
            <form action="#" method="POST">
                <input type="text" id="otp" class="form-control" name="otp_code" required placeholder="Enter Your OTP">
                <input type="submit" value="Verify" name="verify" class="submit-btn">
            </form>
        </div>
    </div>
</body>

</html>

<?php
session_start();
require 'db_connection.php';

if (isset($_POST["verify"])) {
    $otp = $_SESSION['otp'];
    $email = $_SESSION['mail'];
    $otp_code = $_POST['otp_code'];

    if ($otp != $otp_code) {
        echo "<script>alert('Invalid OTP code');</script>";
    } else {
        if ($_SESSION['Hospital'] == TRUE) {
            // Retrieve hospital data from session
            $hospitalName = $_SESSION['hospital_data']['hospitalName'];
            $address = $_SESSION['hospital_data']['address'];
            $contact = $_SESSION['hospital_data']['contact'];
            $email = $_SESSION['hospital_data']['email'];
            $username = $_SESSION['hospital_data']['username'];
            $password = $_SESSION['hospital_data']['password'];
            $userLevel = $_SESSION['hospital_data']['userLevel'];

            // Generate a new hospital ID
            $query = $db->query("SELECT hospitalId FROM hospitals ORDER BY hospitalId DESC LIMIT 1");
            $new_hospital_id = "H00000000001"; // Default starting ID

            if ($query->num_rows > 0) {
                $row = $query->fetch_assoc();
                $last_id = $row['hospitalId'];
                $num = (int) substr($last_id, 1) + 1;
                $new_hospital_id = "H" . str_pad($num, 11, "0", STR_PAD_LEFT);
            }

            // Insert hospital data into the database
            $sql = "INSERT INTO hospitals (hospitalId, hospitalName, address, contact, email, username, password, userLevel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            if ($stmt === false) {
                die("Prepare failed: " . $db->error);
            }

            $stmt->bind_param("sssssssi", $new_hospital_id, $hospitalName, $address, $contact, $email, $username, $password, $userLevel);

            if ($stmt->execute()) {
                // Clear session data
                session_unset();
                session_destroy();
                session_start();

                // Redirect to login page with success message
                $_SESSION['success_alert_message'] = "Successfully Registered Hospital!";
                header("Location: login.php");
                exit();
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } elseif ($_SESSION['Donor'] == TRUE) {
            // Retrieve donor data from session
            $firstName = $_SESSION['user_data']['firstName'];
            $lastName = $_SESSION['user_data']['lastName'];
            $NICnumber = $_SESSION['user_data']['NICnumber'];
            $weight = $_SESSION['user_data']['weight'];
            $bloodGroup = $_SESSION['user_data']['bloodGroup'];
            $duration = $_SESSION['user_data']['duration'];
            $dateOfBirth = $_SESSION['user_data']['dateOfBirth'];
            $gender = $_SESSION['user_data']['gender'];
            $address = $_SESSION['user_data']['address'];
            $personalContact = $_SESSION['user_data']['personalContact'];
            $emergencyContact = $_SESSION['user_data']['emergencyContact'];
            $email = $_SESSION['user_data']['email'];
            $username = $_SESSION['user_data']['username'];
            $password = $_SESSION['user_data']['password'];
            $eligibilityStatus = $_SESSION['user_data']['eligibilityStatus'];

            // Generate a new donor ID
            $query = $db->query("SELECT Donorid FROM donors ORDER BY Donorid DESC LIMIT 1");
            $new_donor_id = "D00000000001"; // Default starting ID

            if ($query->num_rows > 0) {
                $row = $query->fetch_assoc();
                $last_id = $row['Donorid'];
                $num = (int) substr($last_id, 1) + 1;
                $new_donor_id = "D" . str_pad($num, 11, "0", STR_PAD_LEFT);
            }

            // Insert donor data into the database
            $sql = "INSERT INTO donors (Donorid, firstName, lastName, NICnumber, weight, bloodGroup, donationDuration, dateOfBirth, gender, address, personalContact, emergencyContact, email, username, password, eligibilityStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            if ($stmt === false) {
                die("Prepare failed: " . $db->error);
            }

            $stmt->bind_param("ssssisissssssssi", $new_donor_id, $firstName, $lastName, $NICnumber, $weight, $bloodGroup, $duration, $dateOfBirth, $gender, $address, $personalContact, $emergencyContact, $email, $username, $password, $eligibilityStatus);

            if ($stmt->execute()) {
                // Clear session data
                session_unset();
                session_destroy();
                session_start();

                // Redirect to login page with success message
                $_SESSION['success_alert_message'] = "Successfully Registered Donor!";
                header("Location: login.php");
                exit();
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            // Retrieve campaigner data from session
            $name = $_SESSION['campaigner_data']['name'];
            $address = $_SESSION['campaigner_data']['address'];
            $contact = $_SESSION['campaigner_data']['contact'];
            $email = $_SESSION['campaigner_data']['email'];
            $description = $_SESSION['campaigner_data']['description'];
            $username = $_SESSION['campaigner_data']['username'];
            $password = $_SESSION['campaigner_data']['password'];

            // Generate a new campaigner ID
            $query = $db->query("SELECT campaignersId FROM campaigners ORDER BY campaignersId DESC LIMIT 1");
            $new_campaigner_id = "C00000000001"; // Default starting ID

            if ($query->num_rows > 0) {
                $row = $query->fetch_assoc();
                $last_id = $row['campaignersId'];
                $num = (int) substr($last_id, 1) + 1;
                $new_campaigner_id = "C" . str_pad($num, 11, "0", STR_PAD_LEFT);
            }

            // Insert campaigner data into the database
            $sql = "INSERT INTO campaigners (campaignersId, name, address, contact, email, description, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            if ($stmt === false) {
                die("Prepare failed: " . $db->error);
            }

            $stmt->bind_param("ssssssss", $new_campaigner_id, $name, $address, $contact, $email, $description, $username, $password);

            if ($stmt->execute()) {
                // Clear session data
                session_unset();
                session_destroy();
                session_start();

                // Redirect to login page with success message
                $_SESSION['success_alert_message'] = "Successfully Registered Campaigner!";
                header("Location: login.php");
                exit();
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }
    }
}

?>