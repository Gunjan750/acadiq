<?php
include "config.php";
session_start();
$res = "";
$prob = "";
$error = "";
$attendance = "";
$marks = "";
$study_hours = "";

// Load from session if available (after redirect)
if (isset($_SESSION['attendance'])) {
    $attendance = $_SESSION['attendance'];
    $marks = $_SESSION['marks'];
    $study_hours = $_SESSION['study_hours'];
}

if (isset($_SESSION['prediction_result'])) {
    $res = $_SESSION['prediction_result'];
    $prob = $_SESSION['prediction_prob'];
    unset($_SESSION['prediction_result']);
    unset($_SESSION['prediction_prob']);
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['predict'])) {

    // Sanitize + validate inputs
    $attendance = floatval($_POST['attendance']);
    $marks = floatval($_POST['marks']);
    $study_hours = floatval($_POST['study_hours']);

    // Save to session for redisplay after redirect
    $_SESSION['attendance'] = $attendance;
    $_SESSION['marks'] = $marks;
    $_SESSION['study_hours'] = $study_hours;

    if ($attendance < 0 || $attendance > 100) {
        $error = "Attendance must be between 0 and 100!";
    } elseif ($marks < 0 || $marks > 100) {
        $error = "Marks must be between 0 and 100!";
    } elseif ($study_hours < 0) {
        $error = "Study hours must be positive!";
    } else {

        // Safe command execution
        $python = "C:\\Users\\Admin\\AppData\\Local\\Programs\\Python\\Python314\\python.exe";
        $script = "C:\\xampp\\htdocs\\acadiq\\predict.py";

        $command = 'cmd /c ""' . $python . '" "' . $script . '" '
         . escapeshellarg($attendance) . ' '
         . escapeshellarg($marks) . ' '
         . escapeshellarg($study_hours) . ' 2>&1"';
        $result = shell_exec($command);

        if ($result) {
            $output = trim($result);
            $parts = explode("|", $output);

        if (count($parts) == 2) {
         $res = $parts[0];
         $prob = round($parts[1] * 100, 2);

        // Get logged-in user ID
        $email = $_SESSION['user'];
        $user_query = "SELECT id FROM students WHERE email='$email'";
        $user_result = mysqli_query($conn, $user_query);
        $user = mysqli_fetch_assoc($user_result);
        $student_id = $user['id'];

        $insert = "INSERT INTO predictions 
        (student_id, prediction, attendance, marks, study_hours) 
        VALUES ('$student_id', '$res', '$attendance', '$marks', '$study_hours')";
    
        mysqli_query($conn, $insert);

        // SAVE result for display
        $_SESSION['prediction_result'] = $res;
        $_SESSION['prediction_prob'] = $prob;

        // REDIRECT to avoid form resubmission
        header("Location: predict.php");
        exit();
        }   else {
        echo "<p class='error'>Unexpected output format from Python script.</p>";
    }
        } else {
            $error = "Prediction failed. Check Python setup.";
        }
    }
}
?>
<title>AcadIQ - Predict Performance</title>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class="container">
    <h2>🤖 Predict Student Performance</h2>

     <button style="width: 25%; align-items: center; "><a href="dashboard.php" style="text-decoration: none;">Back to Dashboard</a></button>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="number" name="attendance" placeholder="Attendance (%)" value="<?php if(isset($_POST['attendance'])) echo $_POST['attendance']; ?>" required><br>
        <input type="number" name="marks" placeholder="Marks" value="<?php if(isset($_POST['marks'])) echo $_POST['marks']; ?>" required><br>
        <input type="number" name="study_hours" placeholder="Study Hours" value="<?php if(isset($_POST['study_hours'])) echo $_POST['study_hours']; ?>" required><br>
        <button name="predict">Predict</button>
    </form>

    <!-- Display result -->
    <?php if (!empty($res)) { ?>
        <?php
        $color = ($res == 'Pass') ? '#0caa31' : '#d84854';
        $message = ($res == 'Pass') ? "Great job! Keep it up!" : "Don't worry, focus on improving!";
        $colour = ($res == 'Pass') ? '#a2efb4' : '#f7939b';
        ?>
        <div class="card" style="background-color: <?php echo $colour; ?>;">
            <h2 style="color: <?php echo $color; ?>;"><?php echo $res; ?></h2>
            <p style="text-align: center;"><strong>Confidence: </strong><?php echo $prob; ?>%</p>

            <!--Show current input values-->
            <p style="text-align: center;">
                <strong>
                Attendance: <?php echo $attendance; ?>% |
                Marks: <?php echo $marks; ?> |
                Study Hours: <?php echo $study_hours; ?>
                </strong> 
            </p>

            <!-- Personalized message -->
            <p><?php echo $message; ?></p>
        </div>

        <div class="card">
            <h3>💡Suggestions:</h3>
            <ul>
                <?php
                if ($res == 'Pass') {
                    echo "<li>💪Maintain attendance and marks.</li>";
                    echo "<li>📚Try to increase study hours for even better results.</li>";
                    echo "<li>📈Keep practicing regularly!</li>";
                } else {
                    echo "<li>🎯Focus on improving attendance.</li>";
                    echo "<li>📉Review subjects where marks are low.</li>";
                    echo "<li>📚Consider increasing study hours.</li>";
                }
                ?>
            </ul>
        </div>
<?php   } ?>   
         <!-- History -->
        <h3 style="background-color: #89dbed;">📊Recent Predictions</h3>
        <div class="table-container">
            <table>
                <tr>
                    <th>Date</th>
                    <th>Attendance (%)</th>
                    <th>Marks</th>
                <th>Study Hours</th>
                <th>Result</th>
            </tr>
            <?php
            $email = $_SESSION['user'];
            $query = "SELECT p.prediction, p.created_at, p.attendance, p.marks, p.study_hours FROM predictions p JOIN students s ON p.student_id = s.id WHERE s.email = '$email' ORDER BY p.created_at DESC LIMIT 5";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . date("Y-m-d H:i", strtotime($row['created_at'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['attendance']) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['marks']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['study_hours']) . "</td>";
                    $color = ($row['prediction'] == 'Pass') ? '#0caa31' : '#d84854';
                    echo "<td style='color: $color; font-weight: bold;'>" . htmlspecialchars($row['prediction']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No predictions found.</td></tr>";
            } ?>  
        </table>
</div>

