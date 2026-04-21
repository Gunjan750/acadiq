<?php
session_start();
include "config.php";
$email = $_SESSION['user'];
$query = "SELECT * FROM students WHERE email='$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<link rel="stylesheet" href="style.css">
<div class="container">
    <h2>Welcome, <?php echo $user['name']; ?>!</h2>
    <p>Email: <?php echo $user['email']; ?></p>
    <a href="index.php">View Students</a><br><br>
    <a href="add_student.php">Add Student</a><br><br>
    <a href="logout.php" class="logout-button">Logout</a><br><br>
    <a href="add_marks.php">Add Marks</a><br><br>
    <a href="add_attendence.php">Add Attendance</a><br><br>
    <h3>All Students</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php
        $query = "SELECT * FROM students";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "</tr>";
        }
        ?>
</div>