<?php
session_start();
require '../db_connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$hospitalId = $_SESSION['hospitalId'];

// Fetch data from the database
$sql = "SELECT hospitalName, address, contact, email FROM hospitals WHERE hospitalId = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $hospitalId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Close the statement and connection
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <title>MedAlert</title>
</head>

<body>
    <header class="navbar-container">
        <div class="logo">
            <img src="../../Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
        <div class="nav-links">
            <a href="../logout.php" class="nav-button">Logout</a>
        </div>
    </header>

    <section class="container text-center mt-5">
        <h2>Emergency Blood Need Form</h2>
        <form id="bloodNeedForm" action="emergencyAlert.php" method="POST">
            <div class="form-group">
                <label for="hospitalName">Hospital Name:</label>
                <input type="text" class="form-control" id="hospitalName" name="hospitalName" required value="<?php echo htmlspecialchars($data['hospitalName']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="hospitalLocation">Hospital Location:</label>
                <input type="text" class="form-control" id="hospitalLocation" name="hospitalLocation" required value="<?php echo htmlspecialchars($data['address']); ?>" onchange="updateMessage()">
            </div>
            <div class="form-group">
                <label for="bloodType">Blood Type Needed:</label>
                <select class="form-control" id="bloodType" name="bloodType" required onchange="updateMessage()">
                    <option value="">Select Blood Type</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantityNeeded">Quantity Needed (Units):</label>
                <input type="number" class="form-control" id="quantityNeeded" name="quantityNeeded" required min="1" onchange="updateMessage()">
            </div>
            <div class="form-group">
                <label for="contactPhone">Contact Phone:</label>
                <input type="tel" class="form-control" id="contactPhone" name="contactPhone" required pattern="^(\+94[0-9]{9}|0[0-9]{9})$" title="Please enter a phone number in the format +94XXXXXXXXX or 077XXXXXXXX" value="<?php echo htmlspecialchars($data['contact']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="contactEmail">Contact Email:</label>
                <input type="email" class="form-control" id="contactEmail" name="contactEmail" required value="<?php echo htmlspecialchars($data['email']); ?>" onchange="updateMessage()">
            </div>
            <div class="form-group">
                <input type="hidden" id="emailBody" name="emailBody" value="">
                <label for="defaultMessage">Email Body:</label>
                <div id="formattedMessage" contenteditable="true" class="custom-textarea" onclick="updateMessage()">
                </div>
            </div>
            <button type="submit" class="btn"><span></span><span></span><span></span>
                <span></span>Submit Blood Request</button>
        </form>
    </section>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $emailBody = $_POST['emailBody'];
        $hospitalName = $_POST['hospitalName'];
        $bloodType = $_POST['bloodType'];

        // Get eligible donors
        $sqlDonors = "SELECT email FROM donors WHERE eligibilityStatus = 1";
        $resultDonors = $db->query($sqlDonors);

        require "../Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer(true);

        if ($resultDonors->num_rows > 0) {
            while ($row = $resultDonors->fetch_assoc()) {

                $donorEmail = $row['email'];

                // Send email to each eligible donor
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Username = 'jeyandrankeerthika5@gmail.com';
                    $mail->Password = 'mojfidopvduiutfs';
                    $mail->setFrom('jeyandrankeerthika5@gmail.com', 'MedAlert');
                    $mail->addAddress($donorEmail);
                    $mail->isHTML(true);
                    $mail->Subject = "Urgent: Blood Type  $bloodType Needed at $hospitalName";
                    $mail->Body = "<b>Subject: Urgent Blood Need at $hospitalName</b> <br>" . $emailBody . "<p>From MedAlert</p>";

                    if (!$mail->send()) {
                        echo "Email could not be sent to $donorEmail.";
                    }
                } catch (Exception $e) {
                    echo "Error sending email to $donorEmail: " . $mail->ErrorInfo;
                }
            }
        }

        // Get email addresses of hospitals
        $sqlHospitals = "SELECT email FROM hospitals WHERE hospitalId != ?";
        $stmtHospitals = $db->prepare($sqlHospitals);
        $stmtHospitals->bind_param("s", $hospitalId);
        $stmtHospitals->execute();
        $resultHospitals = $stmtHospitals->get_result();

        if ($resultHospitals->num_rows > 0) {
            while ($row = $resultHospitals->fetch_assoc()) {
                $hospitalEmail = $row['email'];

                // Send email to each hospital
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Username = 'jeyandrankeerthika5@gmail.com';
                    $mail->Password = 'mojfidopvduiutfs';
                    $mail->setFrom('jeyandrankeerthika5@gmail.com', 'MedAlert');
                    $mail->addAddress($hospitalEmail);
                    $mail->isHTML(true);
                    $mail->Subject = "Urgent: Blood Type  $bloodType Needed at $hospitalName";
                    $mail->Body = "<b>Subject: Urgent Blood Need at $hospitalName</b> <br>" . $emailBody . "<p>From MedAlert</p>";

                    if (!$mail->send()) {
                        echo "Email could not be sent to $hospitalEmail.";
                    } else {
    ?>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
                        <script>
                            function generateQRCode(eventId, campaignerEmail, eventName) {
                                const padding = 20; // Define the padding size
                                const qr = qrcode(1, 'L');
                                qr.addData(eventId);
                                qr.make();

                                const cellSize = 10; // Size of each QR code cell
                                const qrSize = qr.getModuleCount() * cellSize; // Size of the QR code
                                const canvasSize = qrSize + 2 * padding; // Total canvas size including padding

                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                canvas.width = canvasSize;
                                canvas.height = canvasSize;

                                // Draw white background
                                ctx.fillStyle = '#ffffff';
                                ctx.fillRect(0, 0, canvas.width, canvas.height);

                                // Draw the QR code
                                for (let row = 0; row < qr.getModuleCount(); row++) {
                                    for (let col = 0; col < qr.getModuleCount(); col++) {
                                        ctx.fillStyle = qr.isDark(row, col) ? '#000000' : '#ffffff';
                                        ctx.fillRect(
                                            col * cellSize + padding,
                                            row * cellSize + padding,
                                            cellSize,
                                            cellSize
                                        );
                                    }
                                }

                                const dataUrl = canvas.toDataURL('image/png');
                                saveQRCode(dataUrl, eventId, campaignerEmail, eventName);
                            }


                            function saveQRCode(dataUrl, eventId, campaignerEmail, eventName) {
                                console.log(campaignerEmail)
                                console.log(eventName)
                                fetch('generate_qr.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: `eventid=${eventId}&image=${encodeURIComponent(dataUrl)}&campaignerEmail=${encodeURIComponent(campaignerEmail)}&eventName=${encodeURIComponent(eventName)}`
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert("Check your email to get the attendance QR!");
                                        } else {
                                            alert('Error saving QR code.');
                                        }
                                    })
                            }
                            generateQRCode('<?php echo $hospitalId; ?>', '<?php echo $data['email']; ?>', 'Emergency Blood Request');
                        </script>
    <?php
                    }
                } catch (Exception $e) {
                    echo "Error sending email to $hospitalEmail: " . $mail->ErrorInfo;
                }
            }
        }

        $db->close();
    }
    ?>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function updateMessage() {
            const bloodType = document.getElementById('bloodType').value;
            const hospitalName = document.getElementById('hospitalName').value;
            const hospitalLocation = document.getElementById('hospitalLocation').value;
            const contactPhone = document.getElementById('contactPhone').value;
            const contactEmail = document.getElementById('contactEmail').value;
            const quantityNeeded = document.getElementById('quantityNeeded').value;

            const defaultMessage = `
                <p>We urgently need your help! <b>Blood type ${bloodType}</b> is required at <b>${hospitalName}</b> to assist a patient in critical condition. We request your immediate action to help save a life.</p>
                <li><b>Details:</b></li>
                    <li><b>Blood Type Needed:</b> ${bloodType}</li>
                    <li><b>Quantity Needed:</b> ${quantityNeeded} units</li>
                    <li><b>Hospital:</b> ${hospitalName}, ${hospitalLocation}</li>
                    <li><b>Phone:</b> ${contactPhone} | <b>Email:</b> ${contactEmail}</li>
                <p><b>Donation Process:</b> Please visit the hospital’s blood bank at the earliest to make your donation. No prior appointment is required, and the process typically takes 30 minutes.</p>
                <p>Your contribution could save a life today. We sincerely appreciate your willingness to help.</p>
                <p>Thank you!</p>
            `;

            document.getElementById('formattedMessage').innerHTML = defaultMessage;
            document.getElementById('emailBody').value = defaultMessage;
        }
    </script>
</body>

</html>