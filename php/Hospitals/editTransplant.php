<?php
require '../db_connection.php';

// Retrieve advertisement data based on the ID from query parameter
$adId = '';
$advertisement = [];

if (isset($_GET['kidneyTransplantAdvertisementId'])) {
    $adId = $_GET['kidneyTransplantAdvertisementId'];
    $sql = "SELECT * FROM kidneytransplantadvertisement WHERE kidneyTransplantAdvertisementId=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $adId);
    $stmt->execute();
    $result = $stmt->get_result();
    $advertisement = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<p style='color: red;'>No advertisement ID provided.</p>";
    exit();
}

// Fetch hospitals for dropdown
$sql = "SELECT hospitalId, hospitalName FROM hospitals";
$hospitalsResult = $db->query($sql);

// Handle form submission
$updateMessage = '';
$updateSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['kidneyTransplantAdvertisementId']) && isset($_POST['patientName']) && isset($_POST['age']) && isset($_POST['contact']) && isset($_POST['email']) && isset($_POST['description']) && isset($_POST['hospitalId'])) {
        $adId = $_POST['kidneyTransplantAdvertisementId'];
        $patientName = $_POST['patientName'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $hospitalId = $_POST['hospitalId'];
        $adBanner = $advertisement['adBanner'];

        // Handle file upload, if a new file is provided
        if (isset($_FILES['adBanner']) && $_FILES['adBanner']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "../../TransplantAd/";
            $targetFile = $targetDir . basename($_FILES["adBanner"]["name"]);
            if (move_uploaded_file($_FILES["adBanner"]["tmp_name"], $targetFile)) {
                $adBanner = $targetFile; // Update banner if a new file is uploaded
            }
        }

        // Check if the email already exists for another advertisement
        $sql = "SELECT COUNT(*) FROM kidneytransplantadvertisement WHERE email=? AND kidneyTransplantAdvertisementId!=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $email, $adId);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();
        $stmt->close();

        if ($emailCount > 0) {
            $updateMessage = "Email is already in use by another advertisement.";
        } else {
            // Update advertisement record
            $sql = "UPDATE kidneytransplantadvertisement SET patientName=?, age=?, contact=?, email=?, description=?, hospitalId=?, adBanner=? WHERE kidneyTransplantAdvertisementId=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sissssss', $patientName, $age, $contact, $email, $description, $hospitalId, $adBanner, $adId);

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
    <title>Edit Kidney Transplant Advertisement</title>
    <link rel="stylesheet" href="../../css/editTransplant.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
                // If update was successful, reload the page
                if (message === "Record updated successfully!") {
                    setTimeout(function() {
                        window.location.href = "../../php/Hospitals/HospitalDashBoard.php";
                    }, 500); // Delay to ensure the alert is visible
                }
            }
        }

        function goBack() {
            window.location.href = '../../php/Hospitals/HospitalDashBoard.php';
        }
    </script>
    <style>
        .auth-form input, 
        .auth-form textarea,
        .auth-form select {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--Dred);
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-form textarea {
            height: 120px;
            resize: vertical;
        }

        .auth-form input:focus,
        .auth-form textarea:focus,
        .auth-form select:focus {
            border-color: var(--Lred);
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
            outline: none;
        }

    </style>
</head>
<body onload="showAlert('<?php echo htmlspecialchars($updateMessage); ?>')">
    <h1>Edit Kidney Transplant Advertisement</h1>
    <div class="auth-form">
        <form action="editTransplant.php?kidneyTransplantAdvertisementId=<?php echo htmlspecialchars($adId); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="kidneyTransplantAdvertisementId" value="<?php echo htmlspecialchars($advertisement['kidneyTransplantAdvertisementId'] ?? ''); ?>">

            <label for="patientName">Recipient Name</label>
            <input type="text" id="patientName" name="patientName" value="<?php echo htmlspecialchars($advertisement['patientName'] ?? ''); ?>" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($advertisement['age'] ?? ''); ?>" required>

            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($advertisement['contact'] ?? ''); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($advertisement['email'] ?? ''); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($advertisement['description'] ?? ''); ?></textarea>

            <label for="hospitalId">Hospital</label>
            <select id="hospitalId" name="hospitalId" required>
                <?php
                while ($hospital = $hospitalsResult->fetch_assoc()) {
                    $selected = $hospital['hospitalId'] == $advertisement['hospitalId'] ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($hospital['hospitalId']) . '" ' . $selected . '>' . htmlspecialchars($hospital['hospitalId']) . ' - ' . htmlspecialchars($hospital['hospitalName']) . '</option>';
                }
                ?>
            </select>

            <label for="adBanner">Advertisement Banner</label>
            <input type="file" id="adBanner" name="adBanner">

            <?php if (!empty($advertisement['adBanner'])) { ?>
                <div class="banner-preview">
                    <img src="<?php echo htmlspecialchars($advertisement['adBanner']); ?>" alt="Current Advertisement Banner" class="ad-banner">
                </div>
            <?php } ?>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Update Advertisement</button>
                <button type="button" onclick="goBack()" class="btn btn-secondary">Back to Dashboard</button>
            </div>
        </form>
    </div>
</body>
</html>
