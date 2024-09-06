<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
$donorId = $_SESSION['donorId'];
$activeSection = isset($_SESSION['activeSection']) ? $_SESSION['activeSection'] : 'profile';

$donorQuery = "SELECT donationDuration, eligibilityStatus FROM donors WHERE Donorid = ?";
$stmt = $db->prepare($donorQuery);
$stmt->bind_param("s", $donorId);
$stmt->execute();
$donorResult = $stmt->get_result();
$donorData = $donorResult->fetch_assoc();

$donationDuration = $donorData['donationDuration'];
$currentEligibilityStatus = $donorData['eligibilityStatus'];

// Fetch the latest donation history for the donor
$historyQuery = "SELECT e.date AS event_date FROM blooddonationhistory bh  JOIN events e ON bh.eventid = e.eventid WHERE bh.donorId = ? ORDER BY e.date DESC LIMIT 1";
$stmt = $db->prepare($historyQuery);
$stmt->bind_param("s", $donorId);
$stmt->execute();
$historyResult = $stmt->get_result();
$historyData = $historyResult->fetch_assoc();

$eligibilityStatus = 1;

if ($historyData) {
    $lastDonationDate = $historyData['event_date'];
    $donationDurationMonths = $donationDuration == 3 ? 3 : 6;

    // Calculate the eligibility date
    $eligibilityDate = date('Y-m-d', strtotime("+$donationDurationMonths months", strtotime($lastDonationDate)));
    $currentDate = date('Y-m-d');

    // Check if the donor is eligible based on the last donation date
    if ($currentDate < $eligibilityDate) {
        $eligibilityStatus = 0;
    }
}

// Update the eligibility status if it's different from the current status
if ($eligibilityStatus != $currentEligibilityStatus) {
    $updateQuery = "UPDATE donors SET eligibilityStatus = ? WHERE Donorid = ?";
    $stmt = $db->prepare($updateQuery);
    $stmt->bind_param("is", $eligibilityStatus, $donorId);
    $stmt->execute();
}

$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/hospital.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <title>MedAlert</title>
</head>

<body>
    <header class="navbar-container_long">
        <div class="logo">
            <img src="../../Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
        <div class="activeNav mt-3">
            <div id="nav-toggle" class="nav-toggle_long">☰</div>
            <div class="nav-links nav-links_long">
                <a href="setSession.php?section=profile" class="nav-button"> Profile </a>
                <a href="setSession.php?section=ScanQr" class="nav-button"> Attendance Scanner</a>
                <a href="setSession.php?section=DonationHistory" class="nav-button"> Donation History</a>
                <a href="../logout.php" class="nav-button"> Logout</a>
            </div>
        </div>
    </header>

    <div id="profile" class="section <?php echo $activeSection == 'profile' ? 'active' : ''; ?>">
        <?php
        $stmt = $db->prepare("SELECT firstName, lastName, NICnumber, weight, bloodGroup, donationDuration, dateOfBirth, gender, address, personalContact, emergencyContact, email, eligibilityStatus, username FROM donors WHERE DonorId = ?");
        $stmt->bind_param("s", $donorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $donor = $result->fetch_assoc();
        $stmt->close();
        ?>
        <div class="card my-3 mx-2">
            <div class="card-body text-center">
                <h2 class="card-title my-4 ">Donor Details</h2>
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($donor['firstName']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($donor['lastName']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="NICnumber" class="form-label">NIC Number</label>
                    <input type="text" class="form-control" id="NICnumber" name="NICnumber" value="<?php echo htmlspecialchars($donor['NICnumber']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (kg)</label>
                    <input type="number" class="form-control" id="weight" name="weight" value="<?php echo htmlspecialchars($donor['weight']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="bloodGroup" class="form-label">Blood Group</label>
                    <input type="text" class="form-control" id="bloodGroup" name="bloodGroup" value="<?php echo htmlspecialchars($donor['bloodGroup']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="donationDuration" class="form-label">Donation Duration</label>
                    <input type="text" class="form-control" id="donationDuration" name="donationDuration" value="<?php echo htmlspecialchars($donor['donationDuration']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="dateOfBirth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($donor['dateOfBirth']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($donor['gender']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" readonly><?php echo htmlspecialchars($donor['address']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="personalContact" class="form-label">Personal Contact</label>
                    <input type="text" class="form-control" id="personalContact" name="personalContact" value="<?php echo htmlspecialchars($donor['personalContact']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="emergencyContact" class="form-label">Emergency Contact</label>
                    <input type="text" class="form-control" id="emergencyContact" name="emergencyContact" value="<?php echo htmlspecialchars($donor['emergencyContact']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($donor['email']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($donor['username']); ?>" readonly>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                    Edit Details
                </button>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Donor Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="update_donor.php">
                            <div class="form-group">
                                <label for="firstName">First Name:</label>
                                <input type="text" name="firstName" id="firstName" class="form-control" required pattern="^[a-zA-Z]+$" value="<?php echo htmlspecialchars($donor['firstName']); ?>" title="First name should contain only letters!">
                            </div>

                            <div class="form-group">
                                <label for="lastName">Last Name:</label>
                                <input type="text" name="lastName" id="lastName" class="form-control" required pattern="^[a-zA-Z]+$" value="<?php echo htmlspecialchars($donor['lastName']); ?>" title="Last name should contain only letters!">
                            </div>

                            <div class="form-group">
                                <label for="NICnumber">NIC Number:</label>
                                <input type="text" name="NICnumber" id="NICnumber" class="form-control" required pattern="^(\d{9}V|\d{12})$" value="<?php echo htmlspecialchars($donor['NICnumber']); ?>" title="NIC should be 9 digits followed by 'V' (e.g., 123456789V) or 12 digits (e.g., 123456789012)">
                            </div>

                            <div class="form-group">
                                <label for="weight">Weight (kg):</label><br>
                                <label for="weight" class="text-danger">* You should be 50kg or above 50kg to donate blood</label>
                                <input type="number" name="weight" id="weight" class="form-control" min="50" required value="<?php echo htmlspecialchars($donor['weight']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="bloodGroup">Select Blood Type:</label>
                                <select id="bloodGroup" name="bloodGroup" class="form-control" required>
                                    <option value="<?php echo htmlspecialchars($donor['bloodGroup']); ?>" selected><?php echo htmlspecialchars($donor['bloodGroup']); ?></option>
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
                                <label for="donationDuration">How often can you donate blood:</label><br>
                                <select class="form-control" name="donationDuration" id="donationDuration" required>
                                    <option value="<?php echo htmlspecialchars($donor['donationDuration']); ?>" selected><?php echo htmlspecialchars($donor['donationDuration']); ?> months</option>
                                    <option value="3">Every 3 months</option>
                                    <option value="6">Every 6 months</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dateOfBirth">Date of Birth:</label><br>
                                <label for="weight" class="text-danger">* Your age should be between 18 - 65 to donate blood</label>
                                <input type="date" name="dateOfBirth" id="dateOfBirth" class="form-control" required value="<?php echo htmlspecialchars($donor['dateOfBirth']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="<?php echo htmlspecialchars($donor['gender']); ?>" selected><?php echo htmlspecialchars($donor['gender']); ?></option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="address">Address:</label>
                                <textarea name="address" id="address" class="form-control" required><?php echo htmlspecialchars($donor['address']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="personalContact">Personal Contact:</label>
                                <input type="text" name="personalContact" id="personalContact" class="form-control" required pattern="^\+94\d{9}$" value="<?php echo htmlspecialchars($donor['personalContact']); ?>" title="Personal Contact should start with +94 followed by 9 digits">
                            </div>

                            <div class="form-group">
                                <label for="emergencyContact">Emergency Contact:</label>
                                <label class="text-danger">*Personal Contact and Emergency Contact shouldn't be Same!</label>
                                <input type="text" name="emergencyContact" id="emergencyContact" class="form-control" required pattern="^\+94\d{9}$" value="<?php echo htmlspecialchars($donor['emergencyContact']); ?>" title="Emergency Contact should start with +94 followed by 9 digits">
                            </div>

                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" required value="<?php echo htmlspecialchars($donor['email']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" name="username" id="username" class="form-control" required value="<?php echo htmlspecialchars($donor['username']); ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="ScanQr" class="section <?php echo $activeSection == 'ScanQr' ? 'active' : ''; ?>">
        <div id="camera-container">
            <video id="scanner" autoplay></video>
        </div>
        <form id="qrForm" action="QrScan.php" method="POST" style="display: none;">
            <input type="hidden" id="qrDataInput" name="qrData" value="">
        </form>
    </div>

    <div id="DonationHistory" class="section <?php echo $activeSection == 'DonationHistory' ? 'active' : ''; ?>">
        <div class="container text-center mt-4">
            <h2 class="my-4 text-center">Donation History</h2>
            <span class="badge py-3 px-4 mb-3 badge-<?php echo $eligibilityStatus == 1 ? 'success' : 'danger'; ?>"><?php echo $eligibilityStatus == 1 ? "Can Donate" : "Can't Donate"; ?></span>
            <table class="table small-font table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Campaigner Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT bh.Historyid, bh.eventid, bh.donorId, e.name AS event_name, e.date AS event_date, c.name AS campaigner_name 
                        FROM blooddonationhistory bh
                        JOIN events e ON bh.eventid = e.eventid
                        JOIN campaigners c ON e.campaignersId = c.campaignersId
                        WHERE bh.donorId = ? 
                        ORDER BY e.date DESC";

                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('s', $donorId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['event_name']) . "</td>
                                <td>" . htmlspecialchars($row['event_date']) . "</td>
                                <td>" . htmlspecialchars($row['campaigner_name']) . "</td>
                              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No donation history found</td></tr>";
                    }

                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>


    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../../Script/script.js"></script>
<script src="https://unpkg.com/jsqr/dist/jsQR.js"></script>
<script>
    const video = document.getElementById('scanner');
    const resultElement = document.getElementById('result');
    const canvas = document.createElement('canvas');
    const canvasContext = canvas.getContext('2d', {
        willReadFrequently: true
    });
    let timeoutId = null;

    function startVideo() {
        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user"
                }
            })
            .then(stream => {
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(scanQRCode);

                timeoutId = setTimeout(() => {
                    if (confirm("Invalid QR code. Please try again.")) {
                        location.reload();
                    }
                }, 60000);
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
            });
    }

    function scanQRCode() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, canvas.width, canvas.height, {
                inversionAttempts: "dontInvert",
            });

            if (code) {
                const qrData = code.data;
                video.srcObject.getTracks().forEach(track => track.stop());

                clearTimeout(timeoutId);

                if (confirm(`Do you want to mark your attendance?`)) {
                    document.getElementById('qrDataInput').value = qrData;
                    document.getElementById('qrForm').submit();
                }
            } else {
                requestAnimationFrame(scanQRCode); // Continue scanning
            }
        } else {
            requestAnimationFrame(scanQRCode);
        }
    }

    window.onload = startVideo;
</script>

</html>