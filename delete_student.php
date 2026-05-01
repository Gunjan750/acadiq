<?php
include "config.php";

$id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM students WHERE id=$id");
header("Location: index.php");
exit();
?>