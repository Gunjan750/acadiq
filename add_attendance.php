<?php
include "config.php";

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    $student_id = intval($_POST['student_id']);
    $percentage = floatval($_POST['percentage']);

    // Validation
    if ($student_id <= 0) {
        $error = "Invalid student ID!";
    } elseif ($percentage < 0 || $percentage > 100) {
        $error = "Attendance must be between 0 and 100!";
    } else {

        // Check if student exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM students WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 0) {
            $error = "Student does not exist!";
        } else {

            // Insert attendance
            $stmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, percentage) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "id", $student_id, $percentage);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Attendance added successfully!";
            } else {
                $error = "Failed to add attendance!";
            }
        }
    }
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AcadIQ - Add Attendance</title>
<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>Add Attendance</h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <input type="number" name="student_id" placeholder="Student ID" required><br><br>
        <input type="number" name="percentage" placeholder="Attendance (%)" min="0" max="100" step="0.1" required><br><br>
        <button type="submit" name="submit">Add Attendance</button>
    </form>

    <br><button style="width: 25%; align-items: center; "><a href="dashboard.php" style="text-decoration: none;">Back to Dashboard</a></button>
</div>