<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to MedAlert.">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="Images/logo.png" type="image/png">
    <title>MedAlert - Login</title>
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
                <a href="./php/login.php" class="nav-button">Login</a>
                <a href="./php/signup.php" class="nav-button">Signup</a>
            </div>
        </div>
    </header>

    <section id="signup">
        <h2>Create Your Account</h2>
        <form action="process_signup.php" method="post" class="auth-form">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit" class="btn auth-button"><span></span><span></span><span></span><span></span>Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </section>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>

</html>
