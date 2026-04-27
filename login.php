<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {

    // Sanitize inputs
    $username = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Use prepared statement (prevents SQL injection)
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE name=? AND email=?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        // Simple password check (upgrade to hashed later if needed)
        if ($password === $row['password']) {
            $_SESSION['user'] = $row['email'];
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

<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
</div>