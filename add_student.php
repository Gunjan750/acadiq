<?
include "config.php";
if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "INSERT INTO students (name, email, password) VALUES ('$name', '$email', '$password')";
    mysqli_query($conn, $query); 
        echo "New student added successfully";
}   ?>
<form method="POST">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="submit">Add Student</button>
</form>