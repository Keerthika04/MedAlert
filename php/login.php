<?php
session_start();
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statements to check each user type
    $queries = [
        "donor" => "SELECT Donorid, username, password FROM donors WHERE username = ?",
        "campaigner" => "SELECT campaignersId, username, password FROM campaigners WHERE username = ?",
        "hospital" => "SELECT hospitalId,username, password, userLevel FROM hospitals WHERE username = ?"
    ];

    $userType = null;
    $hashedPassword = null;

    // Check donors table
    if ($stmt = $db->prepare($queries['donor'])) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($donorId,$dbUsername, $dbPassword);
        if ($stmt->fetch() && password_verify($password, $dbPassword)) {
            $_SESSION['donorId'] = $donorId;
            $userType = 'donor';
        }
        $stmt->close();
    }

    // Check campaigners table
    if (!$userType && $stmt = $db->prepare($queries['campaigner'])) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($campaignersId,$dbUsername, $dbPassword);
        if ($stmt->fetch() && password_verify($password, $dbPassword)) {
            $_SESSION['campaignersId'] = $campaignersId;
            $userType = 'campaigner';
        }
        $stmt->close();
    }

    // Check hospitals table
    if (!$userType && $stmt = $db->prepare($queries['hospital'])) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hospitalId,$dbUsername, $dbPassword, $userLevel);
        if ($stmt->fetch() && password_verify($password, $dbPassword)) {
            $userType = 'hospital';
            $_SESSION['userLevel'] = $userLevel;
            $_SESSION['hospitalId'] = $hospitalId;
        }
        $stmt->close();
    }

    if ($userType) {
        $_SESSION['username'] = $username;

        // Redirect based on user type
        switch ($userType) {
            case 'donor':
                header("Location: ./Donors/DonorsDashboard.php");
                break;
            case 'campaigner':
                header("Location: ./Campaigner/CampaignerDashboard.php");
                break;
            case 'hospital':
                header("Location: ./Hospitals/HospitalDashBoard.php");
                break;
        }
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid username or password.";
        header("Location: login.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../Images/logo.png" type="image/png">
    <title>MedAlert</title>
</head>

<body>
    <header class="navbar-container">
        <div class="logo">
            <img src="../Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
    </header>

    <section id="login" class="d-flex justify-content-center flex-column text-center">
        <h2>Login to Your Account</h2>
        <form action="login.php" method="post" class="auth-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn auth-button"><span></span><span></span><span></span>
                <span></span>Login</button>
        </form>
        <p>Don't have an account? <a href="../signup_selection.html">Sign up here</a>.</p>
        <br>
        <?php if (isset($_SESSION['success_alert_message'])) {
            echo "<div class='alert alert-success mt-3'>" . htmlspecialchars($_SESSION['success_alert_message']) . "</div>";
            unset($_SESSION['success_alert_message']);
        }
        ?>
        <?php if (isset($_SESSION['error_message'])) {
            echo "<div class='alert alert-danger mt-3'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
            unset($_SESSION['error_message']);
        }
        ?>
    </section>
    <footer class="bFooter">
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>