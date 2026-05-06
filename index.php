<?php
session_start();
include "config.php";

// 🔐 Protect page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM students";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>AcadIQ - Students Index</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<div class="container">

    <h2>📋 Student List</h2>

    <div class="nav-links">
        <a href="add_student.php">➕ Add Student</a>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="logout.php" class="logout-button">🚪 Logout</a>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                <th>Action</th>
            </tr>

            <?php
            
            if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <a href='edit_student.php?id={$row['id']}'>Edit</a> |
                            <a href='delete_student.php?id={$row['id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                        </td>
                      </tr>";
            }
            } else {
                echo "<tr><td colspan='3'>No students found.</td></tr>";
            }
            ?>

        </table>
    </div>

</div>
</body>
</html>