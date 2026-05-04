<?php
session_start();
include "config.php";

// 🔐 Check login FIRST
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];

// Get user details safely
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE email=?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Prepare chart data
$marks = [];
$attendance = [];

$stmt = mysqli_prepare($conn, "SELECT m.marks, a.percentage 
    FROM marks m 
    JOIN attendance a ON m.student_id = a.student_id 
    WHERE m.student_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $marks[] = $row['marks'];
    $attendance[] = $row['percentage'];
}

// Average Marks
$avgMarksQuery = mysqli_query($conn, "SELECT AVG(marks) as avg_marks FROM marks");
$avgMarksRow = mysqli_fetch_assoc($avgMarksQuery);
$avg_marks = round($avgMarksRow['avg_marks'], 2);

// Average Attendance
$avgAttendanceQuery = mysqli_query($conn, "SELECT AVG(percentage) as avg_attendance FROM attendance");
$avgAttendanceRow = mysqli_fetch_assoc($avgAttendanceQuery);
$avg_attendance = round($avgAttendanceRow['avg_attendance'], 2);

//Insight Logic
if ($avg_marks > 85 && $avg_attendance > 90) {
    $insight = "Overall performance is excellent! 🌟";
} elseif ($avg_marks > 70 && $avg_attendance > 80) {
    $insight = "Performance is good, needs a bit more effort. 👍";
} elseif ($avg_marks > 50 && $avg_attendance > 60) {
    $insight = "Performance is moderate, needs improvement. 📈";
} else {
    $insight = "Performance is low, immediate attention required. 🚨";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="container">

    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h2>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <div class="nav-links">
        <a href="index.php">Students</a>
        <a href="add_student.php">Add Student</a>
        <a href="add_marks.php">Add Marks</a>
        <a href="add_attendance.php">Add Attendance</a>
        <a href="predict.php">Predict Performance</a>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <!--Analytics Cards-->
    <div class="analytics">
    <div class="card">
        <h3>Average Marks</h3>
        <p><?php echo $avg_marks; ?></p>
    </div>

    <div class="card">
        <h3>Average Attendance</h3>
        <p><?php echo $avg_attendance; ?>%</p>
    </div>

    <div class="card">
        <h3>Performance Insight</h3>
        <p><?php echo $insight; ?></p>
    </div>
    </div>

    <!-- Students -->
    <div class="card">
        <h3>All Students</h3>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th></tr>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM students");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                      </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Marks -->
    <div class="card">
        <h3>Marks</h3>
        <table>
            <tr><th>Student ID</th><th>Subject ID</th><th>Marks</th></tr>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM marks");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['student_id']}</td>
                        <td>{$row['subject_id']}</td>
                        <td>{$row['marks']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Attendance -->
    <div class="card">
        <h3>Attendance</h3>
        <table>
            <tr><th>Student ID</th><th>Attendance (%)</th></tr>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM attendance");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['student_id']}</td>
                        <td>{$row['percentage']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Performance -->
    <div class="card">
        <h3>Performance Overview</h3>
        <table>
            <tr><th>Name</th><th>Marks</th><th>Attendance</th></tr>
            <?php
            $query = "SELECT s.name, m.marks, a.percentage 
                      FROM students s
                      JOIN marks m ON s.id = m.student_id
                      JOIN attendance a ON s.id = a.student_id";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>{$row['marks']}</td>
                        <td>{$row['percentage']}</td>
                      </tr>";
            }
            ?>
        </table>
    </div>

    <!-- Chart -->
    <div class="card">
        <h3>Performance Graph</h3>
        <canvas id="myChart"></canvas>
    </div>

</div>

<script>
const marks = <?php echo json_encode($marks); ?>;
const attendance = <?php echo json_encode($attendance); ?>;

new Chart(document.getElementById('myChart'), {
    type: 'bar',
    data: {
        labels: marks.map((_, i) => "Record " + (i + 1)),
        datasets: [
            { label: 'Marks', data: marks },
            { label: 'Attendance (%)', data: attendance }
        ]
    }
});
</script>
</body>
</html>