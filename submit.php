<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@gmail.com') {
    header("Location: log.html");
    exit();
}

$servername = "sql208.infinityfree.com";
$username   = "if0_39876995";
$password   = "smart2552";
$database   = "if0_39876995_nithish";

$con = new mysqli($servername, $username, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Since there's no 'id' column, no way to update a single row by id.
// So remove this update block if you can't identify rows uniquely.

// Fetch all feedback (no status column filtering, no ORDER BY id)
$feedback = $con->query("SELECT * FROM feedback");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - SmartSuggest</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
<h1>Admin Dashboard</h1>

<?php if ($feedback && $feedback->num_rows > 0): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Report Type</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $feedback->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= htmlspecialchars($row['Rating']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['Comments'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No feedback found.</p>
<?php endif; ?>

</body>
</html>

<?php
$con->close();
?>
