<?php
session_start();
include "config.php";
$email = $_SESSION['user'];
$query = "SELECT * FROM students WHERE email='$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$marks = [];
$attendance = [];

$query = "SELECT m.marks, a.percentage FROM marks m JOIN attendance a ON m.student_id = a.student_id WHERE m.student_id = '{$user['id']}'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $marks[] = $row['marks'];
    $attendance[] = $row['percentage'];
}

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
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
    <h3>Student Marks</h3>
        <table>
            <tr>
                <th>Student ID</th>
                <th>Subject ID</th>
                <th>Marks</th>
            </tr>
            <?php
            $query = "SELECT * FROM marks";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['student_id'] . "</td>";
                echo "<td>" . $row['subject_id'] . "</td>";
                echo "<td>" . $row['marks'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h3>Attendance Records</h3>
        <table>
            <tr>
                <th>Student ID</th>
                <th>Attendance (%)</th>
            </tr>
            <?php
            $query = "SELECT * FROM attendance";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['student_id'] . "</td>";
                echo "<td>" . $row['percentage'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h3>Student Performance Overview</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Marks</th>
                <th>Attendance (%)</th>
            </tr>
            <?php
            $query = "SELECT s.name, m.marks, a.percentage FROM students s
                      JOIN marks m ON s.id = m.student_id
                      JOIN attendance a ON s.id = a.student_id";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['marks'] . "</td>";
                echo "<td>" . $row['percentage'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        </div>
        <h3>Performance Graph</h3>
        <canvas id="myChart"></canvas>
         <script>
            const marks = <?php echo json_encode($marks); ?>;
            const attendance = <?php echo json_encode($attendance); ?>;
            const ctx = document.getElementById('myChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: marks.map((_, i) => "Student " + (i + 1)),
                    datasets: [
                        {
                            label: 'Marks',
                            data: marks,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Attendance (%)',
                            data: attendance,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }
                    ]
                }
            });
        </script>
</body>
</html>
