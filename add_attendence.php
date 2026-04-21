<?php
include "config.php";

if(isset($_POST['submit'])) {
    $student_id = $_POST['student_id'];
    $percentage = $_POST['percentage'];

    $query = "INSERT INTO attendance (student_id, percentage) VALUES ('$student_id', '$percentage')";
    if (mysqli_query($conn, $query)) {
        echo "Attendance added successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<link rel="stylesheet" href="style.css">
<div class="container">
    <h2>Add Attendance</h2>
    <form method="POST" action="">
        Student ID: <input type="number" name="student_id" required><br><br>
        Attendance (%): <input type="number" name="percentage" required><br><br>
        <input type="submit" name="submit" value="Add Attendance">
    </form>
    <br><a href="dashboard.php">Back to Dashboard</a>
</div>