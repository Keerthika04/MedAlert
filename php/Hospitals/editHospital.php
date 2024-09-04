<?php
require '../db_connection.php';

// Retrieve hospital data based on the ID from query parameter
$hospitalId = '';
$hospital = [];

if (isset($_GET['hospitalId'])) {
    $hospitalId = $_GET['hospitalId'];
    $sql = "SELECT * FROM hospitals WHERE hospitalId=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $hospitalId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hospital = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<p style='color: red;'>No hospital ID provided.</p>";
    exit();
}

// Handle form submission
$updateMessage = '';
$updateSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['hospitalId']) && isset($_POST['hospitalName']) && isset($_POST['username']) && isset($_POST['address']) && isset($_POST['contact']) && isset($_POST['email'])) {
        $hospitalId = $_POST['hospitalId'];
        $hospitalName = $_POST['hospitalName'];
        $username = $_POST['username'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];

        // Check if email or username already exists for another hospital
        $sql = "SELECT COUNT(*) FROM hospitals WHERE (email=? OR username=?) AND hospitalId<>?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sss', $email, $username, $hospitalId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $updateMessage = "Email or Username already exists. Please choose a different one.";
        } else {
            // Update hospital record
            $sql = "UPDATE hospitals SET hospitalName=?, username=?, address=?, contact=?, email=? WHERE hospitalId=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ssssss', $hospitalName, $username, $address, $contact, $email, $hospitalId);

            if ($stmt->execute()) {
                $updateMessage = "Record updated successfully!";
                $updateSuccess = true;
            } else {
                $updateMessage = "Error updating record: " . $db->error;
            }

            $stmt->close();
        }
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
    <title>Edit Hospital</title>
    <link rel="stylesheet" href="../../css/editHospital.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
                // If update was successful, reload the page
                if (message === "Record updated successfully!") {
                    setTimeout(function() {
                        window.location.href = "../../php/Hospitals/HospitalDashBoard.php?hospitalId=<?php echo htmlspecialchars($hospitalId); ?>";
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
    <h1>Edit Hospital Information</h1>
    <div class="auth-form">
        <form action="editHospital.php?hospitalId=<?php echo htmlspecialchars($hospitalId); ?>" method="post">
            <input type="hidden" name="hospitalId" value="<?php echo htmlspecialchars($hospital['hospitalId'] ?? ''); ?>">

            <label for="hospitalName">Hospital Name</label>
            <input type="text" id="hospitalName" name="hospitalName" value="<?php echo htmlspecialchars($hospital['hospitalName'] ?? ''); ?>" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($hospital['username'] ?? ''); ?>" required>

            <label for="address">Address</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($hospital['address'] ?? ''); ?></textarea>

            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($hospital['contact'] ?? ''); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($hospital['email'] ?? ''); ?>" required>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Update Hospital</button>
                <button type="button" onclick="goBack()" class="btn btn-secondary">Back to Dashboard</button>
            </div>
        </form>
    </div>
</body>
</html>
