<?php
include 'db_connection.php';

function generateFeedbackId($db) {
    $query = "SELECT feedbackId FROM feedback ORDER BY feedbackId DESC LIMIT 1";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['feedbackId'];
        $lastNumber = intval(substr($lastId, 1));
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    
    $nextId = 'F' . str_pad($nextNumber, 11, '0', STR_PAD_LEFT);
    return $nextId;
}

function isEmailDuplicate($db, $email) {
    $query = "SELECT COUNT(*) AS count FROM feedback WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    
    return $count > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $feedback = $_POST['message'];

    if (isEmailDuplicate($db, $email)) {
        echo "<script>alert('This email has already been used for feedback. Please use a different email.'); window.location.href = 'feedback.php';</script>";
    } else {
        $feedbackId = generateFeedbackId($db);

        $stmt = $db->prepare("INSERT INTO feedback (feedbackId, name, email, feedback) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $feedbackId, $name, $email, $feedback);

        if ($stmt->execute()) {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('feedback-modal').style.display = 'flex';
                    });
                  </script>";
        } else {
            echo "<script>alert('There was an error submitting your feedback. Please try again.'); window.location.href = 'feedback.php';</script>";
        }

        $stmt->close();
    }

    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MedAlert - Managing emergency blood needs and kidney transplant advertisements in Sri Lanka.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/slider.css">
    <link rel="stylesheet" href="../css/awareness.css">
    <link rel="icon" href="../Images/logo.png" type="image/png">
    <title>MedAlert - Feedback</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0,0,0,0.5);
            padding-top: 60px;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease-out;
        }
        .modal-header {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .modal-body {
            font-size: 16px;
            color: #555;
        }
        .modal-footer {
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
            text-align: center;
        }
        .modal-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4CAF50;
            color: #ffffff;
            font-size: 16px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }
        .modal-footer button.cancel {
            background-color: #f44336;
        }
        .modal-footer button:hover {
            background-color: #45a049;
        }
        .modal-footer button.cancel:hover {
            background-color: #e63946;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
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
            <a href="login.php" class="nav-button">Login</a>
            <a href="../signup_selection.html" class="nav-button">Signup</a>
        </div>
    </div>
</header>

<section id="feedback">
    <div class="feedback-container">
        <h2>We Value Your Feedback</h2>
        <p>Your feedback helps us improve and provide better services. Please share your thoughts with us.</p>
        <form class="feedback-form" action="feedback.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
                <label for="message">Feedback:</label>
                <textarea id="message" name="message" placeholder="Your Feedback" rows="6" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
    </div>
</section>

<!-- Modal HTML -->
<div id="feedback-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Thank You!</h2>
        </div>
        <div class="modal-body">
            <p>Your feedback has been submitted successfully.</p>
        </div>
        <div class="modal-footer">
            <button onclick="window.location.href='../index.php'">Go to Guest Page</button>
            <button class="cancel" onclick="window.location.href='feedback.php'">Continue Giving Feedback</button>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    <p><a href="#">Terms of Service</a></p>
</footer>

<script>
    window.onclick = function(event) {
        var modal = document.getElementById('feedback-modal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
</script>

</body>
</html>
