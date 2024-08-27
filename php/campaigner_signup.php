<?php
session_start();
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
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
    <div class="container text-center mt-5">
        <h2 class="text-center">Campaigner Registration</h2>
        <form method="post" action="campaigner_signup.php" class="mt-4">
            <div class="form-group">
                <label for="name">Campaigner Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="contact">Contact Number:</label>
                <input type="text" name="contact" id="contactNo" class="form-control" required
                    pattern="^(\+94[0-9]{9}|0[0-9]{9})$"
                    placeholder="+94XXXXXXXXX"
                    title="Contact number should start with +94 followed by 9 digits (e.g., +94123456789)">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" id="address" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="description">Campaign Description:</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control"
                    pattern="^(?=.*[A-Z])(?=.*\W).{8,}$"
                    title="Password should be at least 8 characters long, include at least one uppercase letter and one special character."
                    required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label><br>
                <label id="password_error" class="text-danger"></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                    oninput="checkPasswordMatch()"
                    required>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>
        <div class="login-link">
            <span>Already have an account? </span>
            <a href="login.php" class="login-link-text">Login here</a>
        </div>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Variables
            $name = $_POST["name"];
            $address = $_POST["address"];
            $contact = $_POST["contact"];
            $email = $_POST["email"];
            $description = $_POST["description"];
            $username = $_POST["username"];
            $password_hashed = password_hash($_POST["password"], PASSWORD_BCRYPT);

            // Validation
            $errors = array();
            // Check if username already exists
            $sql = "SELECT username, email FROM campaigners";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row["username"] === $username) {
                        $errors[] = "Username is already taken!";
                    }
                    if ($row["email"] === $email) {
                        $errors[] = "Email is already registered!";
                    }
                }
            }

            // If there are no errors, send the OTP via email
            if (empty($errors)) {
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['mail'] = $email;

                // Send OTP through email using PHPMailer
                require "Mail/phpmailer/PHPMailerAutoload.php";
                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';

                $mail->Username = 'jeyandrankeerthika5@gmail.com';
                $mail->Password = 'mojfidopvduiutfs';

                $mail->setFrom('jeyandrankeerthika5@gmail.com', 'OTP Verification');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Your MedAlert One-Time Password (OTP) for Verification";
                $mail->Body = "<p>Dear " . $name . ", <br> <br>Welcome to MedAlert - Your Healthcare Partner! <br> We're thrilled to have you onboard. To ensure the security of your MedAlert account, we need to verify your email address using a One Time Password (OTP).</p> <h4>Your verification OTP code is $otp </h4>
                    <br>
                    <p>Please use this OTP to complete the verification process and gain access to your account. Remember, for your security, do not share this OTP with anyone.
                    <br>If you have any questions or encounter any issues during the login process, please don't hesitate to reach out to our support team at MedAlert Support.
                    <br><b>From MedAlert</b>";

                if (!$mail->send()) {
        ?>
                    <script>
                        alert("<?php echo "Register Failed, Invalid Email " ?>");
                    </script>
                <?php
                } else {
                ?>
                    <script>
                        <?php
                        // Store the campaigner data in session to insert after OTP verification
                        $campaigner_data = array(
                            'name' => $name,
                            'address' => $address,
                            'contact' => $contact,
                            'email' => $email,
                            'description' => $description,
                            'username' => $username,
                            'password' => $password_hashed
                        );
                        $_SESSION['campaigner_data'] = $campaigner_data;
                        $_SESSION['Campaigner'] = TRUE;
                        $_SESSION['Donor'] = FALSE;
                        $_SESSION['Hospital'] = FALSE;
                        ?>

                        alert("<?php echo "Successfully sent the OTP to " . $email ?>");
                        window.location.replace('otp_verification.php');
                    </script>
        <?php
                }
            } else {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script>
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const errorSpan = document.getElementById('password_error');

        if (password !== confirmPassword) {
            errorSpan.textContent = "*Passwords do not match!";
            document.getElementById('confirm_password').setCustomValidity("Passwords do not match!");
            return false; // Prevents form submission
        } else {
            errorSpan.textContent = "";
            document.getElementById('confirm_password').setCustomValidity("");
            return true; // Allows form submission
        }
    }
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>
