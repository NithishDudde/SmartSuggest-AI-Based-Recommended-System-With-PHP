<?php
// Database credentials
$servername = "sql208.infinityfree.com";
$username   = "if0_39876995";
$password   = "smart2552";
$database   = "if0_39876995_nithish";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate input
$uname = trim($_POST['uname']);
$email = trim($_POST['email']);
$pass  = $_POST['pass'];
$confp = $_POST['confp'];

if (empty($uname) || empty($email) || empty($pass) || empty($confp)) {
    die("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

if ($pass !== $confp) {
    die("Passwords do not match.");
}

// Check for existing username or email
$stmt = $conn->prepare("SELECT * FROM user WHERE uname = ? OR email = ?");
$stmt->bind_param("ss", $uname, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Username or email already exists.");
}

// ✅ Insert new user with plain password
$stmt = $conn->prepare("INSERT INTO user (uname, email, pass) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $uname, $email, $pass);

if ($stmt->execute()) {
    echo "<script>alert('Registration successful! Please login.'); window.location.href='log.html';</script>";
} else {
    echo "<script>alert('Error while registering. Try again.'); window.location.href='register.html';</script>";
}

$stmt->close();
$conn->close();
?>
