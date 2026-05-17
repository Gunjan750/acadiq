<?php
include "config.php";

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    // Sanitize inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Backend validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {

        // Check if email exists (prepared statement)
        $stmt = mysqli_prepare($conn, "SELECT id FROM students WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Email already exists!";
        } else {

            // 🔐 Hash password (VERY IMPORTANT)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert student
            $stmt = mysqli_prepare($conn, "INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Student added successfully!";
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AcadIQ - Add Student</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="brand">
    <h1 class="logo-text">AcadIQ</h1>
    <p class="tagline">
        AI-Powered Student Performance Analysis & Prediction System
    </p>
</div>
<div class="container">
    <h2>Add New Student</h2>
    <a href="index.php">Back to Student List</a><br><br>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="submit">Add Student</button>
    </form>

</div>
</body>
</html>