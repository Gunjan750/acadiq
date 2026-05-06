<?php
session_start();
include "config.php";

// 🔐 Check login FIRST
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$role = $_SESSION['role'] ?? 'student'; // Default to 'student' if not set

// Get user details safely
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE email=?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$student_id = $user['id'];

// Prepare chart data
$labels = [];
$data1 = [];
$data2 = [];

if ($role == 'admin') {
   $query = "SELECT s.name, AVG(m.marks) as avg_marks, AVG(a.percentage) as avg_attendance 
             FROM students s
             LEFT JOIN marks m ON s.id = m.student_id
             LEFT JOIN attendance a ON s.id = a.student_id
             WHERE s.role = 'student'
             GROUP BY s.id ORDER BY avg_marks DESC";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['name'];
        $data1[] = round($row['avg_marks'] ?? 0, 2);
        $data2[] = round($row['avg_attendance'] ?? 0, 2);
    }
} else {
    $query = "SELECT sub.subject_name, m.marks, a.percentage 
              FROM students s
              JOIN marks m ON s.id = m.student_id
              JOIN attendance a ON s.id = a.student_id
              JOIN subjects sub ON m.subject_id = sub.id
              WHERE s.id = ? ORDER BY sub.id ASC";
    $result = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($result, "i", $student_id);
    mysqli_stmt_execute($result);
    $result = mysqli_stmt_get_result($result);

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['subject_name'];
        $data1[] = round($row['marks'] ?? 0, 2);
        $data2[] = round($row['percentage'] ?? 0, 2);
    }
}

// Average Marks
if ($role == 'admin') {
    $avgMarksQuery = mysqli_query($conn, "SELECT AVG(marks) as avg_marks FROM marks");
    $avgAttendanceQuery = mysqli_query($conn, "SELECT AVG(percentage) as avg_attendance FROM attendance");
} else {
    $stmt = mysqli_prepare($conn, "SELECT AVG(marks) as avg_marks FROM marks WHERE student_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $avgMarksQuery = mysqli_stmt_get_result($stmt);
    $stmt2 = mysqli_prepare($conn, "SELECT AVG(percentage) as avg_attendance FROM attendance WHERE student_id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $student_id);
    mysqli_stmt_execute($stmt2);
    $avgAttendanceQuery = mysqli_stmt_get_result($stmt2);
}
$avgMarksRow = mysqli_fetch_assoc($avgMarksQuery);
$avg_marks = round($avgMarksRow['avg_marks']?? 0,2);

// Average Attendance
$avgAttendanceRow = mysqli_fetch_assoc($avgAttendanceQuery);
$avg_attendance = round($avgAttendanceRow['avg_attendance']?? 0, 2);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcadIQ - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="container">

    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h2>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <div class="nav-links">
        <?php if ($role == 'admin') { ?>
        <a href="index.php">Students</a>
        <a href="add_student.php">Add Student</a> 
        <a href="add_marks.php">Add Marks</a>
        <a href="add_attendance.php">Add Attendance</a>
        <?php } ?>
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
        <h3><?php echo ($role == 'admin') ? 'All Students' : 'My Profile'; ?></h3>
        <div class="table-container">
            <table>
                <tr><th>ID</th><th>Name</th><th>Email</th></tr>
                <?php
            if ($role == 'admin') {
                $result = mysqli_query($conn, "SELECT * FROM students WHERE role = 'student' ORDER BY id ASC");
            } else {
               $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $student_id");
            }
            ?>
                <?php
            
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
    </div>

    <!-- Marks -->
    <div class="card">
        <h3>Marks</h3>
        <div class="table-container">
            <table>
                <tr><th>Student ID</th><th>Subject ID</th><th>Marks</th></tr>
                <?php
                if ($role == 'admin') {
                $result = mysqli_query($conn, "SELECT * FROM marks ORDER BY student_id ASC");
                } else {
                    $result = mysqli_query($conn, "SELECT * FROM marks WHERE student_id = $student_id ORDER BY subject_id ASC");
                }
                if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['student_id']}</td>
                            <td>{$row['subject_id']}</td>
                        <td>{$row['marks']}</td>
                      </tr>";
            } } else {
                echo "<tr><td colspan='3'>No marks found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Attendance -->
    <div class="card">
        <h3>Attendance</h3>
        <div class="table-container">
            <table>
                <tr><th>Student ID</th><th>Attendance (%)</th></tr>
                <?php
                if ($role == 'admin') {
                    $result = mysqli_query($conn, "SELECT * FROM attendance ORDER BY student_id ASC");
                } else {
                    $result = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = $student_id ORDER BY subject_id ASC");
                }
                if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['student_id']}</td>
                        <td>{$row['percentage']}%</td>
                      </tr>";
            } } else {
                echo "<tr><td colspan='2'>No attendance records found.</td></tr>";
            }
            ?>
        </table>
    </div>
    </div>

    <!-- Performance -->
    <div class="card">
        <h3>Performance Overview</h3>
        <div class="table-container">
            <table>
                <tr><th>Name</th><th>Marks</th><th>Attendance (%)</th></tr>
                <?php
                if ($role == 'admin') {
                    $query = "SELECT s.name, m.marks, a.percentage 
                              FROM students s
                              JOIN marks m ON s.id = m.student_id
                              JOIN attendance a ON s.id = a.student_id
                              WHERE role = 'student'
                              ORDER BY s.id ASC";
                } else {
                    $query = "SELECT s.name, m.marks, a.percentage 
                              FROM students s
                              JOIN marks m ON s.id = m.student_id
                              JOIN attendance a ON s.id = a.student_id
                              WHERE s.id = $student_id
                              ORDER BY m.subject_id ASC";
                }
                $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>{$row['marks']}</td>
                        <td>{$row['percentage']}%</td>
                      </tr>";
            } } else {
                echo "<tr><td colspan='3'>No performance data found.</td></tr>";
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
const labels = <?php echo json_encode($labels); ?>;
const data1 = <?php echo json_encode($data1); ?>;
const data2 = <?php echo json_encode($data2); ?>;

new Chart(document.getElementById('myChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            { label: '<?php echo ($role == 'admin') ? "Avg Marks per Student" : "Marks per Subject"; ?>', data: data1, backgroundColor: 'rgba(75, 192, 192, 0.6)' },
            { label: '<?php echo ($role == 'admin') ? "Average Attendance" : "Attendance (%)"; ?>', data: data2, backgroundColor: 'rgba(153, 102, 255, 0.6)' }
        ]
    }
});
</script>
</body>
</html>