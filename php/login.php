<?php
session_start();
require 'db_connection.php';
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
        <form action="process_login.php" method="post" class="auth-form">
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
    </section>
    <footer class="bFooter">
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>