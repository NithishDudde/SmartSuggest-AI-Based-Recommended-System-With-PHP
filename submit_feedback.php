<?php
header('Content-Type: application/json');

// Connect to DB
$servername = "sql208.infinityfree.com";
$username   = "if0_39876995";
$password   = "smart2552";
$database   = "if0_39876995_nithish";

$con = new mysqli($servername, $username, $password, $database);
if ($con->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get and sanitize input
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$rating   = trim($_POST['rating'] ?? '');
$comments = trim($_POST['comments'] ?? '');

if (!$name || !$email || !$rating) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit();
}

// Insert into DB
$stmt = $con->prepare("INSERT INTO feedback (Name, Email, Rating, Comments) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $rating, $comments);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save feedback']);
}

$stmt->close();
$con->close();
?>
