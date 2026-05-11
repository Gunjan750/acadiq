<?php
include "config.php";

$name = "";
$email = "";
$password = "";
$error = "";
$success = "";

if(isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {

        // Check if email exists
        $check = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");
        if(mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {

            // Hash password (VERY IMPORTANT)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO students (name, email, password)
                      VALUES ('$name', '$email', '$hashed_password')";

            if(mysqli_query($conn, $query)) {
                $success = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>

<title>AcadIQ - Register</title>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="container">
    <h2>📝 Register</h2>

    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <?php if($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>

        <button name="register">Register</button><br><br>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>