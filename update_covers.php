<?php
// Operations: Automatically search Steam for game artwork by title, download header images locally, and update DB.

require 'db.php';

set_time_limit(300);
$uploadsDir = __DIR__ . '/uploads/';

if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

// Fetch all games from your database
$stmt = $pdo->query("SELECT Game_ID, Title FROM Games");
$games = $stmt->fetchAll();

echo "<h2>Fetching Real Game Artwork from Steam...</h2>";
echo "<ul>";

$downloadCount = 0;

foreach ($games as $game) {
    $gameId = $game['Game_ID'];
    $title = $game['Title'];

    // Generate safe local filename (e.g. "Age of Empires IV" -> "age_of_empires_iv.jpg")
    $safeFilename = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $title)) . '.jpg';
    $targetPath = $uploadsDir . $safeFilename;

    $sourceUrl = null;

    // 1. Query Steam Search API for the Game Title
    $searchUrl = "https://store.steampowered.com/api/storesearch/?term=" . urlencode($title) . "&l=english&cc=US";

    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
            "timeout" => 10
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false
        ]
    ];

    $context = stream_context_create($opts);
    $jsonResponse = @file_get_contents($searchUrl, false, $context);

    if ($jsonResponse) {
        $data = json_decode($jsonResponse, true);
        if (!empty($data['items'][0]['id'])) {
            $steamAppId = $data['items'][0]['id'];
            // Direct Steam CDN artwork URL
            $sourceUrl = "https://cdn.akamai.steamstatic.com/steam/apps/{$steamAppId}/header.jpg";
        }
    }

    // 2. Download artwork if found on Steam
    if ($sourceUrl) {
        $imageData = @file_get_contents($sourceUrl, false, $context);

        if ($imageData !== false && strlen($imageData) > 1000) {
            file_put_contents($targetPath, $imageData);

            // Update DB record to point to local file name
            $updateStmt = $pdo->prepare("UPDATE Games SET Cover_Image = ? WHERE Game_ID = ?");
            $updateStmt->execute([$safeFilename, $gameId]);

            echo "<li style='color: green;'><strong>" . htmlspecialchars($title) . "</strong>: Found on Steam & downloaded real artwork to <code>uploads/$safeFilename</code>!</li>";
            $downloadCount++;
            continue;
        }
    }

    echo "<li style='color: orange;'><strong>" . htmlspecialchars($title) . "</strong>: Not found on Steam, using default cover.</li>";
}

echo "</ul>";
echo "<h3>Complete! Downloaded $downloadCount real game covers.</h3>";
echo "<p><a href='index.php'>Return to Library</a></p>";