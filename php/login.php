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

    <section id="login">
        <h2>Login to Your Account</h2>
        <form action="process_login.php" method="post" class="auth-form">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn auth-button"><span></span><span></span><span></span>
            <span></span>Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </section>

    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>

</html>