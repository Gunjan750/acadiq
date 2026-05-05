<?php
include "config.php";

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete related data first (marks, attendance, predictions)
    mysqli_query($conn, "DELETE FROM marks WHERE student_id=$id");
    mysqli_query($conn, "DELETE FROM attendance WHERE student_id=$id");
    mysqli_query($conn, "DELETE FROM predictions WHERE student_id=$id");

    //Delete student safely
    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Failed to delete student!";
    }
} else {
    echo "Invalid student ID!";
}
?>