<?php
include "config.php";

if(isset($_POST['submit'])) {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $marks = $_POST['marks'];

    $query = "INSERT INTO marks (student_id, subject_id, marks) VALUES ('$student_id', '$subject_id', '$marks')";
    mysqli_query($conn, $query);
    echo "Marks added successfully.";
}
?>
<link rel="stylesheet" href="style.css">
<div class="container">
    <h2>Add Marks</h2>
    <form method="POST" action="">
        Student ID: <input type="number" name="student_id" required><br><br>
        Subject ID: <input type="number" name="subject_id" required><br><br>
        Marks: <input type="number" name="marks" required><br><br>
        <input type="submit" name="submit" value="Add Marks">
    </form>
    <br><a href="dashboard.php">Back to Dashboard</a>
</div>