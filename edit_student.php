<?php
include "config.php";

$id = intval($_GET['id']);

// Fetch student details
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
$error = "";

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE students SET name=?, email=?, password=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $password, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to update student!";
        }
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="brand">
    <h1 class="logo-text">AcadIQ</h1>
    <p class="tagline">
        AI-Powered Student Performance Analysis & Prediction System
    </p>
</div>

<div class="container">
    <h2>Edit Student</h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($student['name']); ?>" required><br><br>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($student['email']); ?>" required><br><br>
        <input type="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($student['password']); ?>" required><br><br>
        <button type="submit" name="update">Update Student</button>
    </form>
</div>