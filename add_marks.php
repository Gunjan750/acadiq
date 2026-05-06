<?php
include "config.php";

$error = "";
$success = "";

if (isset($_POST['submit'])) {

    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $marks = intval($_POST['marks']);

    // Validation
    if ($student_id <= 0 || $subject_id <= 0) {
        $error = "Invalid student or subject ID!";
    } elseif ($marks < 0 || $marks > 100) {
        $error = "Marks must be between 0 and 100!";
    } else {

        // Check if student exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM students WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 0) {
            $error = "Student does not exist!";
        } else {

            // Insert marks safely
            $stmt = mysqli_prepare($conn, "INSERT INTO marks (student_id, subject_id, marks) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iii", $student_id, $subject_id, $marks);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Marks added successfully!";
            } else {
                $error = "Failed to add marks!";
            }
        }
    }
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AcadIQ - Add Marks</title>
<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>Add Marks</h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <input type="number" name="student_id" placeholder="Student ID" required><br><br>
        <input type="number" name="subject_id" placeholder="Subject ID" required><br><br>
        <input type="number" name="marks" placeholder="Marks (0-100)" min="0" max="100" required><br><br>
        <button type="submit" name="submit">Add Marks</button>
    </form>

    <br><button style="width: 25%; align-items: center; "><a href="dashboard.php" style="text-decoration: none;">Back to Dashboard</a></button>
</div>