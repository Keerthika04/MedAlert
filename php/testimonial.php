<?php  
include 'db_connection.php';

$query = "SELECT name, feedback FROM feedback";
$result = $db->query($query);

if (!$result) {
    die("Query failed: " . $db->error);
}

$testimonials = [];
while ($row = $result->fetch_assoc()) {
    $testimonials[] = $row;
}

$db->close();
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
    <link rel="stylesheet" href="../css/testimonial.css">
    <link rel="stylesheet" href="../css/testimonials.css">
    <link rel="icon" href="Images/logo.png" type="image/png">
    <title>MedAlert Testimonials</title>
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

    <section id="testimonials">
        <h2>User Testimonials</h2>
        <div class="testimonial-container">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <img src="../Images/user.jpg" alt="User Image">
                    <div class="testimonial-content">
                        <p>"<?php echo htmlspecialchars($testimonial['feedback']); ?>"</p>
                        <h3><?php echo htmlspecialchars($testimonial['name']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
        <p><a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
