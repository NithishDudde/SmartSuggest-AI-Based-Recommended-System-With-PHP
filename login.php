<?php
session_start();

$mail = $_POST['email'];
$pass = $_POST['pass'];

$servername = "sql208.infinityfree.com";
$username   = "if0_39876995";
$password   = "smart2552";
$database   = "if0_39876995_nithish";

$con = new mysqli($servername, $username, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$sql = "SELECT pass FROM user WHERE email = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $mail);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($db_pass);
    $stmt->fetch();

    if ($pass === $db_pass) {
        $_SESSION['email'] = $mail;

        // Check if it's the admin user
        if ($mail === "admin@gmail.com" && $pass === "admin123") {
            header("Location: admin.php");
            exit();
        } else {
            header("Location: preferences.html");
            exit();
        }
    } else {
        echo "<script>alert('Incorrect password'); window.location.href='log.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Email not found'); window.location.href='log.html';</script>";
    exit();
}

$stmt->close();
$con->close();
?>
