<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: log.html");
    exit();
}

// Database connection
$servername = "sql208.infinityfree.com";
$username   = "if0_39876995";
$password   = "smart2552";
$database   = "if0_39876995_nithish";

$con = new mysqli($servername, $username, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch user details
$email = $_SESSION['email'];
$stmt = $con->prepare("SELECT uname, email FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Profile</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
  /* Base styles */
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f0f4f8;
  margin: 0;
  padding: 40px;
  color: #333;
}

/* Profile container */
.profile-box {
  background-color: #ffffff;
  padding: 40px;
  border-radius: 12px;
  max-width: 480px;
  margin: auto;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  text-align: center;
  animation: fadeIn 0.6s ease-out;
}

/* Heading */
.profile-box h2 {
  font-size: 26px;
  margin-bottom: 20px;
  color: #2c3e50;
}

/* User info */
.info {
  font-size: 16px;
  margin-bottom: 25px;
  color: #555;
}

/* Navigation container */
.action-links {
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: center;
}

/* Button links */
.action-links a {
  display: inline-block;
  padding: 12px 24px;
  border-radius: 6px;
  font-weight: 600;
  text-decoration: none;
  font-size: 15px;
  transition: background-color 0.3s ease, transform 0.2s ease;
  width: 100%;
  max-width: 300px;
  box-sizing: border-box;
}

/* Individual button styles */
a.about {
  background-color: #2ecc71;
  color: #fff;
}
a.about:hover {
  background-color: #27ae60;
  transform: translateY(-2px);
}

a.back {
  background-color: #3498db;
  color: #fff;
}
a.back:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
}

a.logout {
  background-color: #e74c3c;
  color: #fff;
}
a.logout:hover {
  background-color: #c0392b;
  transform: translateY(-2px);
}

/* Icon spacing */
a i {
  margin-right: 6px;
}

/* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Responsive design */
@media (max-width: 600px) {
  body {
    padding: 20px;
  }
  .profile-box {
    padding: 20px;
    max-width: 100%;
  }
  .action-links a {
    font-size: 14px;
    padding: 10px 20px;
  }
}

  </style>
</head>
<body>
  <main class="profile-box" role="main">
    <h2><i class="fas fa-user-circle"></i> Welcome, <?= htmlspecialchars($user['uname']) ?></h2>
    <div class="info"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
    
    <nav class="action-links" aria-label="Profile Actions">
      <a class="back" href="main.html"><i class="fas fa-arrow-left"></i> Back</a>
      <a class="about" href="About.html"><i class="fas fa-user"></i> About</a>
      <a class="logout" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
  </main>
</body>
</html>
