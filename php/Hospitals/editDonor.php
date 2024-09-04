<?php
require '../db_connection.php';

// Retrieve donor data based on the ID from query parameter
$donorId = '';
$donor = [];

if (isset($_GET['donorId'])) {
    $donorId = $_GET['donorId'];
    $sql = "SELECT * FROM donors WHERE Donorid=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $donorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $donor = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<p style='color: red;'>No donor ID provided.</p>";
    exit();
}

// Handle form submission
$updateMessage = '';
$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['donorId']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['username']) && isset($_POST['NICnumber']) && isset($_POST['weight']) && isset($_POST['bloodGroup']) && isset($_POST['dateOfBirth']) && isset($_POST['gender']) && isset($_POST['address']) && isset($_POST['personalContact']) && isset($_POST['emergencyContact']) && isset($_POST['email']) && isset($_POST['eligibilityStatus'])) {
        $donorId = $_POST['donorId'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $username = $_POST['username'];
        $NICnumber = $_POST['NICnumber'];
        $weight = $_POST['weight'];
        $bloodGroup = $_POST['bloodGroup'];
        $dateOfBirth = $_POST['dateOfBirth'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $personalContact = $_POST['personalContact'];
        $emergencyContact = $_POST['emergencyContact'];
        $email = $_POST['email'];
        $eligibilityStatus = $_POST['eligibilityStatus'];

        // Check for duplicate email, NIC, and username in other records
        $sql = "SELECT * FROM donors WHERE (email = ? OR NICnumber = ? OR username = ?) AND Donorid != ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssss', $email, $NICnumber, $username, $donorId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $updateMessage = "Email, NIC, or Username already exists. Please use different values.";
        } else {
            // Update donor record if no duplicates found
            $sql = "UPDATE donors SET firstName=?, lastName=?, username=?, NICnumber=?, weight=?, bloodGroup=?, dateOfBirth=?, gender=?, address=?, personalContact=?, emergencyContact=?, email=?, eligibilityStatus=? WHERE Donorid=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ssssssssssssss', $firstName, $lastName, $username, $NICnumber, $weight, $bloodGroup, $dateOfBirth, $gender, $address, $personalContact, $emergencyContact, $email, $eligibilityStatus, $donorId);

            if ($stmt->execute()) {
                $updateMessage = "Record updated successfully!";
                $updateSuccess = true;
            } else {
                $updateMessage = "Error updating record: " . $db->error;
            }
        }

        $stmt->close();
    } else {
        $updateMessage = "Please fill in all fields.";
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Donor</title>
    <link rel="stylesheet" href="../../css/editDonor.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
                // If update was successful, reload the page
                if (message === "Record updated successfully!") {
                    setTimeout(function() {
                        window.location.href = "../../php/Hospitals/HospitalDashBoard.php?donorId=<?php echo htmlspecialchars($donorId); ?>";
                    }, 500); // Delay to ensure the alert is visible
                }
            }
        }

        function goBack() {
            window.location.href = '../../php/Hospitals/HospitalDashBoard.php';
        }
    </script>
</head>
<body onload="showAlert('<?php echo htmlspecialchars($updateMessage); ?>')">
    <h1>Edit Donor Information</h1>
    <div class="auth-form">
        <form action="editDonor.php?donorId=<?php echo htmlspecialchars($donorId); ?>" method="post">
            <input type="hidden" name="donorId" value="<?php echo htmlspecialchars($donor['Donorid'] ?? ''); ?>">

            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($donor['firstName'] ?? ''); ?>" required>

            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($donor['lastName'] ?? ''); ?>" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($donor['username'] ?? ''); ?>" required>

            <label for="NICnumber">NIC Number</label>
            <input type="text" id="NICnumber" name="NICnumber" value="<?php echo htmlspecialchars($donor['NICnumber'] ?? ''); ?>" required>

            <label for="weight">Weight</label>
            <input type="text" id="weight" name="weight" value="<?php echo htmlspecialchars($donor['weight'] ?? ''); ?>" required>

            <label for="bloodGroup">Blood Group</label>
            <input type="text" id="bloodGroup" name="bloodGroup" value="<?php echo htmlspecialchars($donor['bloodGroup'] ?? ''); ?>" required>

            <label for="dateOfBirth">Date of Birth</label>
            <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($donor['dateOfBirth'] ?? ''); ?>" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo ($donor['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($donor['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($donor['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>

            <label for="address">Address</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($donor['address'] ?? ''); ?></textarea>

            <label for="personalContact">Personal Contact</label>
            <input type="text" id="personalContact" name="personalContact" value="<?php echo htmlspecialchars($donor['personalContact'] ?? ''); ?>" required>

            <label for="emergencyContact">Emergency Contact</label>
            <input type="text" id="emergencyContact" name="emergencyContact" value="<?php echo htmlspecialchars($donor['emergencyContact'] ?? ''); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($donor['email'] ?? ''); ?>" required>

            <label for="eligibilityStatus">Eligibility Status</label>
            <select id="eligibilityStatus" name="eligibilityStatus" required>
                <option value="1" <?php echo ($donor['eligibilityStatus'] ?? '') == '1' ? 'selected' : ''; ?>>Eligible</option>
                <option value="0" <?php echo ($donor['eligibilityStatus'] ?? '') == '0' ? 'selected' : ''; ?>>Not Eligible</option>
            </select>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Update Donor</button>
                <button type="button" onclick="goBack()" class="btn btn-secondary">Back to Dashboard</button>
            </div>
        </form>
    </div>
</body>
</html>
