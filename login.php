<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {

    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Use prepared statement (prevents SQL injection)
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        // Simple password check (upgrade to hashing in production!)
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            echo $row['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found!";
    }
}
?>
<title>AcadIQ - Login</title>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="container">
    <h2>Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
</div>