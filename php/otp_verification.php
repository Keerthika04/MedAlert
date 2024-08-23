<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="UTF-8">
    <title>MedAlert</title>
    <link rel="icon" href="../Images/favicon.png" type="image/png" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/verification.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
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
        // Retrieve donor data from session
        $firstName = $_SESSION['user_data']['firstName'];
        $lastName = $_SESSION['user_data']['lastName'];
        $NICnumber = $_SESSION['user_data']['NICnumber'];
        $weight = $_SESSION['user_data']['weight'];
        $bloodGroup = $_SESSION['user_data']['bloodGroup'];
        $medicalCondition = $_SESSION['user_data']['medicalCondition'];
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
        $sql = "INSERT INTO donors (Donorid, firstName, lastName, NICnumber, weight, bloodGroup, medicalCondition, dateOfBirth, gender, address, personalContact, emergencyContact, email, username, password, eligibilityStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssissssssssssi",$new_donor_id, $firstName, $lastName, $NICnumber, $weight, $bloodGroup, $medicalCondition, $dateOfBirth, $gender, $address, $personalContact, $emergencyContact, $email, $username, $password, $eligibilityStatus);

        if ($stmt->execute()) {
            // Clear session data
            session_unset();
            session_destroy();
            session_start();

            // Redirect to login page with success message
            $_SESSION['success_alert_message'] = "Successfully Registered!";
            header("Location: login.php");
            exit();
        } else {
            echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}
?>