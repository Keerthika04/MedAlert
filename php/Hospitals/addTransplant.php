<?php
session_start();
require '../db_connection.php';

// Fetch hospital IDs and names
$hospitalId = $_SESSION['hospitalId'];
$hospitalSql = "SELECT hospitalId, hospitalName FROM hospitals WHERE hospitalId = ?";
$stmt = $db->prepare($hospitalSql);
$stmt->bind_param("s", $hospitalId);

$stmt->execute();
$hospitalResult = $stmt->get_result();

// Generate a new kidney transplant advertisement ID
function generateNewAdId($db)
{
    $prefix = 'T';
    $length = 11; // Total length of the ID including prefix
    $sql = "SELECT MAX(kidneyTransplantAdvertisementId) AS maxId FROM kidneytransplantadvertisement";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $maxId = $row['maxId'];

    if ($maxId) {
        // Extract the numeric part and increment
        $num = (int)substr($maxId, 1); // Remove prefix and convert to integer
        $num++;
    } else {
        // If no IDs exist, start with 00000000001
        $num = 1;
    }

    $newId = $prefix . str_pad($num, $length - 1, '0', STR_PAD_LEFT); // Format with leading zeros
    return $newId;
}

$newAdId = generateNewAdId($db);

// Handle form submission
$addMessage = '';
$addSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['patientName']) && isset($_POST['age']) && isset($_POST['contact']) && isset($_POST['email']) && isset($_POST['description']) && isset($_POST['hospitalId'])) {
        $patientName = $_POST['patientName'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $description = $_POST['description'];
        $hospitalId = $hospitalId;
        $adBanner = '';

        // Check if email already exists
        $emailCheckSql = "SELECT email FROM kidneytransplantadvertisement WHERE email = ?";
        $stmt = $db->prepare($emailCheckSql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $addMessage = "The email address is already in use. Please use a different email.";
        } else {
            // Handle file upload
            if (isset($_FILES['adBanner']) && $_FILES['adBanner']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "../../TransplantAd/";
                $targetFile = $targetDir . basename($_FILES["adBanner"]["name"]);
                if (move_uploaded_file($_FILES["adBanner"]["tmp_name"], $targetFile)) {
                    $adBanner = $targetFile;
                }
            }

            // Insert new advertisement record
            $sql = "INSERT INTO kidneytransplantadvertisement (kidneyTransplantAdvertisementId, patientName, age, contact, email, description, hospitalId, adBanner) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);

            // Correct data types: 's' for strings and 'i' for integers
            $stmt->bind_param('ssssssss', $newAdId, $patientName, $age, $contact, $email, $description, $hospitalId, $adBanner);

            if ($stmt->execute()) {
                $addMessage = "Record added successfully!";
                $addSuccess = true;
            } else {
                $addMessage = "Error adding record: " . $db->error;
            }
        }

        $stmt->close();
    } else {
        $addMessage = "Please fill in all fields.";
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Kidney Transplant Advertisement</title>
    <link rel="stylesheet" href="../../css/transplant.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <script>
        function validateForm() {
            // Get all form fields
            const patientName = document.getElementById('patientName').value.trim();
            const age = document.getElementById('age').value.trim();
            const contact = document.getElementById('contact').value.trim();
            const email = document.getElementById('email').value.trim();
            const description = document.getElementById('description').value.trim();
            const hospitalId = document.getElementById('hospitalId').value.trim();
            const adBanner = document.getElementById('adBanner').value.trim();

            // Check if all fields are filled
            if (!patientName || !age || !contact || !email || !description || !hospitalId || !adBanner) {
                alert("Please fill in all fields before submitting.");
                return false; // Prevent form submission
            }

            // Allow form submission if all fields are filled
            return true;
        }

        function showAlert(message) {
            if (message) {
                alert(message);
                // If addition was successful, redirect to the management page
                if (message === "Record added successfully!") {
                    setTimeout(function() {
                        window.location.href = "../../php/Hospitals/HospitalDashBoard.php";
                    }, 500);
                }
            }
        }

        function goBack() {
            window.location.href = 'HospitalDashBoard.php';
        }
    </script>
    <style>
        /* Advertisement Banner Styling */
        .auth-form input[type="file"] {
            border: 1px solid var(--Dred);
            border-radius: 8px;
            padding: 12px;
            background-color: var(--white);
            font-size: 16px;
            color: var(--Dred);
            cursor: pointer;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }

        .auth-form input[type="file"]::file-selector-button {
            background-color: var(--Dred);
            color: var(--white);
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            margin-right: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .auth-form input[type="file"]::file-selector-button:hover {
            background-color: var(--Lred);
        }

        .auth-form input[type="file"]:focus {
            border-color: var(--Lred);
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
            outline: none;
        }

        .btn-primary {
            background-color: #7f0101;
        }

        .btn-secondary {
            background-color: #7f0101;
        }
    </style>
</head>

<body onload="showAlert('<?php echo htmlspecialchars($addMessage); ?>')">
    <h1>Add Kidney Transplant Advertisement</h1>
    <div class="auth-form">
        <form action="addTransplant.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="kidneyTransplantAdvertisementId" value="<?php echo htmlspecialchars($newAdId); ?>">

            <label for="patientName">Recipient Name</label>
            <input type="text" id="patientName" name="patientName" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" required>

            <label for="contact">Contact Number</label>
            <input type="text" id="contact" name="contact" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>

            <label for="hospitalId">Hospital</label>
                <?php
                if ($hospitalResult->num_rows > 0) {
                    while ($row = $hospitalResult->fetch_assoc()) {
                        echo "<input value=\"" . htmlspecialchars($row['hospitalName']) . "\" name='hospitalId' readonly>";
                    }
                }
                ?>

            <label for="adBanner">Advertisement Banner</label>
            <input type="file" id="adBanner" name="adBanner" required accept=".jpg, .jpeg, .png">

            <!-- Button Container -->
            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Add Advertisement</button>
                <button type="button" onclick="goBack()" class="btn btn-secondary">Back</button>
            </div>

        </form>
    </div>
</body>

</html>