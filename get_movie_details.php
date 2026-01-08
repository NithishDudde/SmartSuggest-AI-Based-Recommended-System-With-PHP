<?php 
header('Content-Type: application/json');

// Get title from request
$title = $_GET['title'] ?? '';
if (!$title) {
    echo json_encode(["error" => "No title provided"]);
    exit;
}

// CSV URL
$csvUrl = "https://raw.githubusercontent.com/YBI-Foundation/Dataset/main/Movies%20Recommendation.csv";
$csvData = @file_get_contents($csvUrl);

if ($csvData === false) {
    echo json_encode(["error" => "Unable to fetch CSV file"]);
    exit;
}

// Parse CSV
$rows = array_map("str_getcsv", explode("\n", $csvData));
$headers = array_shift($rows);

// 🔹 Clean headers (trim spaces, remove BOM)
$headers = array_map(function($h) {
    return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim($h));
}, $headers);

foreach ($rows as $row) {
    if (count($row) < count($headers)) continue; // skip bad rows
    $movie = array_combine($headers, $row);

    // 🔹 Use case-insensitive match
    if (strcasecmp(trim($movie['Movie_Title']), trim($title)) === 0) {
        echo json_encode($movie);
        exit;
    }
}

// If not found
echo json_encode(["error" => "Movie not found"]);
