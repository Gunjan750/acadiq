<?php
$output = "";
$error = "";

if (isset($_POST['predict'])) {

    // Sanitize + validate inputs
    $attendance = floatval($_POST['attendance']);
    $marks = floatval($_POST['marks']);
    $study = floatval($_POST['study']);

    if ($attendance < 0 || $attendance > 100) {
        $error = "Attendance must be between 0 and 100!";
    } elseif ($marks < 0 || $marks > 100) {
        $error = "Marks must be between 0 and 100!";
    } elseif ($study < 0) {
        $error = "Study hours must be positive!";
    } else {

        // 🔐 Safe command execution
        $python = "C:\\Users\\Admin\\AppData\\Local\\Programs\\Python\\Python314\\python.exe";
        $script = "C:\\xampp\\htdocs\\acadiq\\predict.py";

        $command = 'cmd /c ""' . $python . '" "' . $script . '" '
         . escapeshellarg($attendance) . ' '
         . escapeshellarg($marks) . ' '
         . escapeshellarg($study) . ' 2>&1"';
        $result = shell_exec($command);

        if ($result) {
            $output = trim($result);
        } else {
            $error = "Prediction failed. Check Python setup.";
        }
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>🤖 Predict Student Performance</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="number" name="attendance" placeholder="Attendance (%)" value="<?php if(isset($_POST['attendance'])) echo $_POST['attendance']; ?>" required><br>
        <input type="number" name="marks" placeholder="Marks" value="<?php if(isset($_POST['marks'])) echo $_POST['marks']; ?>" required><br>
        <input type="number" name="study" placeholder="Study Hours" value="<?php if(isset($_POST['study'])) echo $_POST['study']; ?>" required><br>
        <button name="predict">Predict</button>
    </form>

   <?php
if (!empty($output)) {

    $parts = explode("|", $output);

    if (count($parts) == 2) {
        $res = $parts[0];
        $prob = round($parts[1] * 100, 2);
        ?>

        <div class="card">
            <h3 class="success">Prediction: <?php echo $res; ?></h3>
            <p>Confidence: <?php echo $prob; ?>%</p>
        </div>

        <?php
    } else {
        echo "<p class='error'>Unexpected output format from Python script.</p>";
    }
}
?>