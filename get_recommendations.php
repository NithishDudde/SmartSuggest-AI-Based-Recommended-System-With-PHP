<?php
// 🔹 CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 🔹 Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🔹 Remote CSV URL
$url = 'https://github.com/YBI-Foundation/Dataset/raw/main/Movies%20Recommendation.csv';

// 🔹 Fetch CSV
$csvContent = file_get_contents($url);
if ($csvContent === false) {
    echo json_encode(['error' => 'Failed to fetch dataset']);
    exit;
}

// 🔹 Parse CSV
$lines = explode(PHP_EOL, $csvContent);
$headers = str_getcsv(array_shift($lines));
$movies = [];

foreach ($lines as $line) {
    $row = str_getcsv($line);
    if (count($row) < 7) continue;

    $movies[] = [
        'Movie_ID'     => (int) $row[0],
        'title'        => $row[1],
        'genre'        => $row[2],
        'keywords'     => $row[3],
        'tagline'      => $row[4],
        'cast'         => $row[5],
        'director'     => $row[6],
        'image_url'    => "https://via.placeholder.com/300x400.png?text=" . urlencode($row[1])
    ];
}

// 🔹 Text to Vector
function textToVector($text) {
    $words = preg_split('/\s+/', strtolower($text));
    $vector = [];
    foreach ($words as $w) {
        $w = preg_replace('/[^a-z0-9]/', '', $w);
        if ($w === '') continue;
        $vector[$w] = ($vector[$w] ?? 0) + 1;
    }
    return $vector;
}

// 🔹 Cosine Similarity
function cosineSimilarity($vec1, $vec2) {
    $dot = 0.0;
    $normA = 0.0;
    $normB = 0.0;
    foreach ($vec1 as $key => $val) {
        $dot += $val * ($vec2[$key] ?? 0);
        $normA += $val ** 2;
    }
    foreach ($vec2 as $val) {
        $normB += $val ** 2;
    }
    if ($normA == 0 || $normB == 0) return 0;
    return $dot / (sqrt($normA) * sqrt($normB));
}

// 🔹 Recommend Movies
function recommendMovies($movieTitle, $movies, $numRecommendations = 10) {
    $titles = array_column($movies, 'title');
    $closest = null;
    $shortest = -1;
    foreach ($titles as $title) {
        $lev = levenshtein(strtolower($movieTitle), strtolower($title));
        if ($lev == 0) {
            $closest = $title;
            break;
        }
        if ($lev <= $shortest || $shortest < 0) {
            $closest = $title;
            $shortest = $lev;
        }
    }

    if (!$closest) return [];

    foreach ($movies as $m) {
        if ($m['title'] === $closest) {
            $baseMovie = $m;
            break;
        }
    }

    if (!isset($baseMovie)) return [];

    $baseText = $baseMovie['genre'] . " " . $baseMovie['keywords'] . " " .
                $baseMovie['tagline'] . " " . $baseMovie['cast'] . " " .
                $baseMovie['director'];
    $baseVec = textToVector($baseText);

    $scores = [];
    foreach ($movies as $m) {
        if ($m['title'] === $baseMovie['title']) continue;
        $text = $m['genre'] . " " . $m['keywords'] . " " .
                $m['tagline'] . " " . $m['cast'] . " " .
                $m['director'];
        $vec = textToVector($text);
        $similarity = cosineSimilarity($baseVec, $vec);
        $scores[] = ['movie' => $m, 'score' => $similarity];
    }

    usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);
    return array_map(fn($item) => $item['movie'], array_slice($scores, 0, $numRecommendations));
}

// 🔹 Main Execution
if (isset($_GET['movie'])) {
    $movieTitle = trim($_GET['movie']);
    if (empty($movies)) {
        echo json_encode(['error' => 'No movies data available']);
        exit;
    }
    $results = recommendMovies($movieTitle, $movies);
    echo json_encode($results);
} else {
    echo json_encode(['error' => 'No movie parameter provided']);
}
?>
