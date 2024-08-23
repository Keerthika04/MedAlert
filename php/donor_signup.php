<?php
session_start();
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to MedAlert.">
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
        <div class="activeNav">
            <div id="nav-toggle" class="nav-toggle">☰</div>
            <div class="nav-links">
                <a href="./php/login.php" class="nav-button">Login</a>
                <a href="./php/signup.php" class="nav-button">Signup</a>
            </div>
        </div>
    </header>
    <div class="container text-center mt-5">
        <h2 class="text-center">Donor Registration</h2>
        <form method="post" action="donor_signup.php" class="mt-4">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="NICnumber">NIC Number:</label>
                <input type="text" name="NICnumber" id="NICnumber" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="weight">Weight (kg):</label><br>
                <label for="weight" class="text-danger">* You should be 50kg or above 50kg to donate blood</label>
                <input type="number" name="weight" id="weight" class="form-control" min="50" required>
            </div>

            <div class="form-group">
                <label for="bloodGroup">Select Blood Type:</label>
                <select id="bloodGroup" name="bloodGroup" class="form-control" required>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="form-group">
                <label for="medicalCondition">Medical Condition:</label>
                <input type="text" name="medicalCondition" id="medicalCondition" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="dateOfBirth">Date of Birth:</label><br>
                <label for="weight" class="text-danger">* Your age should be between 18 - 65  to donate blood</label>
                <input type="date" name="dateOfBirth" id="dateOfBirth" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" id="address" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="personalContact">Personal Contact:</label>
                <input type="text" name="personalContact" id="personalContact" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="emergencyContact">Emergency Contact:</label>
                <input type="text" name="emergencyContact" id="emergencyContact" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <div class="login-link">
            <span>Already have an account? </span>
            <a href="login.php" class="login-link-text">Login here</a>
        </div>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Variables
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $NICnumber = $_POST["NICnumber"];
            $weight = $_POST["weight"];
            $bloodGroup = $_POST["bloodGroup"];
            $medicalCondition = $_POST["medicalCondition"];
            $dateOfBirth = $_POST["dateOfBirth"];
            $gender = $_POST["gender"];
            $address = $_POST["address"];
            $personalContact = $_POST["personalContact"];
            $emergencyContact = $_POST["emergencyContact"];
            $email = $_POST["email"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $eligibilityStatus = true;

            // Validation
            $errors = array();
            $age = date_diff(date_create($dateOfBirth), date_create('today'))->y;

            if (empty($weight) || $weight < 50) $errors[] = "You must be at least 50kg to donate blood";
            if ($age < 18 || $age > 65) $errors[] = "You must be between 18 and 65 years old to donate blood";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";

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
                $mail->Body = "<p>Dear " . $firstName . " " . $lastName . ", <br> <br>Welcome to MedAlert - Your Healthcare Partner! <br> We're thrilled to have you onboard. To ensure the security of your MedAlert account, we need to verify your email address using a One Time Password (OTP).</p> <h4>Your verification OTP code is $otp </h4>
                    <br>
                    <p>Please use this OTP to complete the verification process and gain access to your account. Remember, for your security, do not share this OTP with anyone.
                    <br>If you have any questions or encounter any issues during the login process, please don't hesitate to reach out to our support team at MedAlert Support.
                    <b>From MedAlert</b>";

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
                        $password_hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        // Store the user data in session to insert after OTP verification
                        $user_data = array(
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'NICnumber' => $NICnumber,
                            'weight' => $weight,
                            'bloodGroup' => $bloodGroup,
                            'medicalCondition' => $medicalCondition,
                            'dateOfBirth' => $dateOfBirth,
                            'gender' => $gender,
                            'address' => $address,
                            'personalContact' => $personalContact,
                            'emergencyContact' => $emergencyContact,
                            'email' => $email,
                            'eligibilityStatus' => $eligibilityStatus,
                            'username' => $username,
                            'password' => $password_hashed
                        );
                        $_SESSION['user_data'] = $user_data;
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>