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
        <h2 class="text-center">Donor Registration</h2>
        <form method="post" action="donor_signup.php" class="mt-4">
            <div class="form-group">
                <label class="text-danger">* Note: If you have medical condition like HIV/AIDS, Hepatitis B or C, Cancer (especially blood cancers like leukemia or lymphoma) and Heart Disease, then you can't donate blood.</label>
            </div>
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" class="form-control" required pattern="^[a-zA-Z]+$"
                    title="First name should contain only letters!">
            </div>

            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" class="form-control" required
                    pattern="^[a-zA-Z]+$"
                    title="Last name should contain only letters!">
            </div>

            <div class="form-group">
                <label for="NICnumber">NIC Number:</label>
                <input type="text" name="NICnumber" id="NICnumber" class="form-control" required
                    pattern="^(\d{9}V|\d{12})$"
                    title="NIC should be 9 digits followed by 'V' (eg: 123456789V) or 12 digits (eg: 123456789012)">
            </div>

            <div class="form-group">
                <label for="weight">Weight (kg):</label><br>
                <label for="weight" class="text-danger">* You should be 50kg or above 50kg to donate blood</label>
                <input type="number" name="weight" id="weight" class="form-control" min="50" required>
            </div>

            <div class="form-group">
                <label for="bloodGroup">Select Blood Type:</label>
                <select id="bloodGroup" name="bloodGroup" class="form-control" required>
                    <option value="">Select Blood Type</option>
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
                <label for="duration">How often can you donate blood:</label><br>
                <select class="form-control" name="duration" id="duration" required>
                    <option value="">Select an option</option>
                    <option value="3">Every 3 months</option>
                    <option value="6">Every 6 months</option>
                </select>
            </div>

            <div class="form-group">
                <label for="dateOfBirth">Date of Birth:</label><br>
                <label for="weight" class="text-danger">* Your age should be between 18 - 65 to donate blood</label>
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
                <input type="text" name="personalContact" id="personalContact" class="form-control" required
                    pattern="^(\+94[0-9]{9}|0[0-9]{9})$"
                    placeholder="+94XXXXXXXXX"
                    title="Phone number should start with +94 followed by 9 digits (e.g., +94123456789)">
            </div>

            <div class="form-group">
                <label for="emergencyContact">Emergency Contact:</label><br>
                <label id="Contact_error" class="text-danger"></label>
                <input type="text" name="emergencyContact" id="emergencyContact" class="form-control" required
                    oninput="checkPhoneNumber()"
                    pattern="^(\+94[0-9]{9}|0[0-9]{9})$"
                    placeholder="+94XXXXXXXXX"
                    title="Phone number should start with +94 followed by 9 digits (e.g., +94123456789)">
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
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $NICnumber = $_POST["NICnumber"];
            $weight = $_POST["weight"];
            $bloodGroup = $_POST["bloodGroup"];
            $duration = $_POST["duration"];
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

            // Check if username already exists
            $sql = "SELECT username,email,NICnumber FROM donors";
            $result = $db->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row["username"] === $username) {
                        $errors[] = "Username is already taken!";
                    }
                    if ($row["email"] === $email) {
                        $errors[] = "Email is already registered!";
                    }
                    if ($row["NICnumber"] === $NICnumber) {
                        $errors[] = "This NIC Number has already registered!";
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
                $mail->Body = "<p>Dear " . $firstName . " " . $lastName . ", <br> <br>Welcome to MedAlert - Your Healthcare Partner! <br> We're thrilled to have you with us. To ensure the security of your MedAlert account, we need to verify your email address using a One Time Password (OTP).</p> <h4>Your verification OTP code is $otp </h4>
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
                        $password_hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
                        // Store the user data in session to insert after OTP verification
                        $user_data = array(
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'NICnumber' => $NICnumber,
                            'weight' => $weight,
                            'bloodGroup' => $bloodGroup,
                            'duration' => $duration,
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
                        $_SESSION['Donor'] = TRUE;
                        $_SESSION['Hospital'] = FALSE;
                        $_SESSION['Campaigner'] = FALSE;
                        ?>

                        alert("<?php echo "Successfully sent the OTP to " . $email ?>");
                        window.location.replace('otp_verification.php');
                    </script>
        <?php
                }
            } else {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger mt-3'>" . $error . "</div>";
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
    const today = new Date();
    const minAgeDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    const maxAgeDate = new Date(today.getFullYear() - 65, today.getMonth(), today.getDate());

    const minDateString = minAgeDate.toISOString().split('T')[0];
    const maxDateString = maxAgeDate.toISOString().split('T')[0];

    document.getElementById('dateOfBirth').setAttribute('min', maxDateString);
    document.getElementById('dateOfBirth').setAttribute('max', minDateString);

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

    function checkPhoneNumber() {
        const personalContact = document.getElementById('personalContact').value;
        const emergencyContact = document.getElementById('emergencyContact').value;
        const errorSpan = document.getElementById('Contact_error');

        if (personalContact == emergencyContact) {
            errorSpan.textContent = "*Personal Contact and Emergency Contact shouldn't be Same!";
            document.getElementById('emergencyContact').setCustomValidity("Personal Contact and Emergency Contact shouldn't be Same!");
            return false; // Prevents form submission
        } else {
            errorSpan.textContent = "";
            document.getElementById('emergencyContact').setCustomValidity("");
            return true; // Allows form submission
        }
    }
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>