<?php
require '../db_connection.php';

// Retrieve campaigner data based on the ID from query parameter
$campaignerId = '';
$campaigner = [];

if (isset($_GET['campaignerId'])) {
    $campaignerId = $_GET['campaignerId'];
    $sql = "SELECT * FROM campaigners WHERE campaignersId=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $campaignerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $campaigner = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<p style='color: red;'>No campaigner ID provided.</p>";
    exit();
}

// Handle form submission
$updateMessage = '';
$updateSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['campaignerId']) && isset($_POST['name']) && isset($_POST['address']) && isset($_POST['contact']) && isset($_POST['email']) && isset($_POST['description']) && isset($_POST['username'])) {
        $campaignerId = $_POST['campaignerId'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $username = $_POST['username'];

        // Check if the email already exists for another campaigner
        $sql = "SELECT COUNT(*) FROM campaigners WHERE email=? AND campaignersId!=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $email, $campaignerId);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();
        $stmt->close();

        if ($emailCount > 0) {
            $updateMessage = "Email is already in use by another campaigner.";
        } else {
            // Check if the username already exists for another campaigner
            $sql = "SELECT COUNT(*) FROM campaigners WHERE username=? AND campaignersId!=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ss', $username, $campaignerId);
            $stmt->execute();
            $stmt->bind_result($usernameCount);
            $stmt->fetch();
            $stmt->close();

            if ($usernameCount > 0) {
                $updateMessage = "Username is already in use by another campaigner.";
            } else {
                // Update campaigner record
                $sql = "UPDATE campaigners SET name=?, address=?, contact=?, email=?, description=?, username=? WHERE campaignersId=?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('sssssss', $name, $address, $contact, $email, $description, $username, $campaignerId);

                if ($stmt->execute()) {
                    $updateMessage = "Record updated successfully!";
                    $updateSuccess = true;
                } else {
                    $updateMessage = "Error updating record: " . $db->error;
                }

                $stmt->close();
            }
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
    <title>Edit Campaigner</title>
    <link rel="stylesheet" href="../../css/editCampaigner.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
                // If update was successful, reload the page
                if (message === "Record updated successfully!") {
                    setTimeout(function() {
                        window.location.href = "../../php/Hospitals/HospitalDashBoard.php?campaignerId=<?php echo htmlspecialchars($campaignerId); ?>";
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
    <h1>Edit Campaigner Information</h1>
    <div class="auth-form">
        <form action="editCampaigner.php?campaignerId=<?php echo htmlspecialchars($campaignerId); ?>" method="post">
            <input type="hidden" name="campaignerId" value="<?php echo htmlspecialchars($campaigner['campaignersId'] ?? ''); ?>">

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($campaigner['name'] ?? ''); ?>" required>

            <label for="address">Address</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($campaigner['address'] ?? ''); ?></textarea>

            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($campaigner['contact'] ?? ''); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($campaigner['email'] ?? ''); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($campaigner['description'] ?? ''); ?></textarea>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($campaigner['username'] ?? ''); ?>" required>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Update Campaigner</button>
                <button type="button" onclick="goBack()" class="btn btn-secondary">Back to Dashboard</button>
            </div>
        </form>
    </div>
</body>
</html>
